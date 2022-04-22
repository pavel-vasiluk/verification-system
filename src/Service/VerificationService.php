<?php

declare(strict_types=1);

namespace App\Service;

use App\Component\Request\Verification\VerificationConfirmationRequest;
use App\Component\Request\Verification\VerificationCreationRequest;
use App\Component\Response\Verification\VerificationCreationResponse;
use App\Entity\Verification;
use App\Exception\DuplicatedVerificationException;
use App\Middleware\Verification\Confirmation\Handler\ConfirmationHandlerInterface;
use App\Repository\VerificationRepository;
use Doctrine\ORM\EntityManagerInterface;

class VerificationService
{
    private EntityManagerInterface $entityManager;
    private VerificationRepository $verificationRepository;
    private ConfirmationHandlerInterface $confirmationHandler;
    private int $verificationCodeLength;

    public function __construct(
        EntityManagerInterface $entityManager,
        VerificationRepository $verificationRepository,
        ConfirmationHandlerInterface $confirmationHandler,
        int $verificationCodeLength,
    ) {
        $this->entityManager = $entityManager;
        $this->verificationRepository = $verificationRepository;
        $this->confirmationHandler = $confirmationHandler;
        $this->verificationCodeLength = $verificationCodeLength;
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

        $this->entityManager->persist($verification);
        $this->entityManager->flush();
        $this->entityManager->refresh($verification);

        return new VerificationCreationResponse($verification->getId()?->toString());
    }

    public function confirmVerification(VerificationConfirmationRequest $request): void
    {
        $this->confirmationHandler->process($request);
    }

    private function generateVerificationCode(): string
    {
        return substr(
            (string) crc32(uniqid('', true)),
            0,
            $this->verificationCodeLength
        );
    }
}
