<?php

declare(strict_types=1);

namespace App\Service;

use App\Component\Request\Verification\VerificationConfirmationRequest;
use App\Component\Request\Verification\VerificationCreationRequest;
use App\Component\Response\Verification\VerificationCreationResponse;
use App\Entity\Verification;
use App\Exception\DuplicatedVerificationException;
use App\Exception\InvalidVerificationCodeException;
use App\Exception\VerificationConfirmationDeniedException;
use App\Exception\VerificationExpiredException;
use App\Exception\VerificationNotFoundException;
use App\Repository\VerificationRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;

class VerificationService
{
    private EntityManagerInterface $entityManager;
    private VerificationRepository $verificationRepository;
    private int $verificationCodeLength;
    private int $verificationLifetime;

    public function __construct(
        EntityManagerInterface $entityManager,
        VerificationRepository $verificationRepository,
        int $verificationCodeLength,
        int $verificationLifetime,
    ) {
        $this->entityManager = $entityManager;
        $this->verificationRepository = $verificationRepository;
        $this->verificationCodeLength = $verificationCodeLength;
        $this->verificationLifetime = $verificationLifetime;
    }

    /**
     * @throws DuplicatedVerificationException
     */
    public function createVerification(VerificationCreationRequest $request): VerificationCreationResponse
    {
        if ($this->verificationRepository->findBySubject($request->getSubject())) {
            throw new DuplicatedVerificationException();
        }

        $verification = (new Verification())
            ->setCode($this->generateVerificationCode())
            ->setSubject($request->getSubject()->jsonSerialize())
            ->setUserInfo($request->getUserInfo()->jsonSerialize())
        ;

        // here need to validate entity before actually persisting

        $this->entityManager->persist($verification);
        $this->entityManager->flush();
        $this->entityManager->refresh($verification);

        return new VerificationCreationResponse($verification->getId()?->toString());
    }

    public function confirmVerification(VerificationConfirmationRequest $request, string $verificationUuid): void
    {
        $verification = $this->verificationRepository->find($verificationUuid);
        $this->validateVerificationConfirmation($verification, $request);

        $verification?->setConfirmed(true);
        $this->entityManager->flush();
    }

    private function generateVerificationCode(): string
    {
        return substr(
            (string) crc32(uniqid('', true)),
            0,
            $this->verificationCodeLength
        );
    }

    /**
     * @throws VerificationNotFoundException
     * @throws VerificationConfirmationDeniedException
     * @throws VerificationExpiredException
     * @throws InvalidVerificationCodeException
     */
    private function validateVerificationConfirmation(
        ?Verification $verification,
        VerificationConfirmationRequest $request
    ): void {
        if (!$verification) {
            throw new VerificationNotFoundException();
        }

        if ($verification->getUserInfo() !== $request->getUserInfo()->jsonSerialize()) {
            throw new VerificationConfirmationDeniedException();
        }

        if ($verification->isConfirmed() || $this->isVerificationExpired($verification)) {
            throw new VerificationExpiredException();
        }

        if ($request->getCode() !== $verification->getCode()) {
            throw new InvalidVerificationCodeException();
        }
    }

    private function isVerificationExpired(Verification $verification): bool
    {
        $now = Carbon::now();

        return $now->diffInMinutes($verification->getCreatedAt()) >= $this->verificationLifetime;
    }
}
