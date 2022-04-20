<?php

declare(strict_types=1);

namespace App\Service;

use App\Component\Request\Template\VerificationCreationRequest;
use App\Component\Request\Template\VerificationCreationResponse;
use App\Entity\Verification;
use App\Exception\DuplicatedVerificationException;
use App\Repository\VerificationRepository;
use Doctrine\ORM\EntityManagerInterface;

class VerificationService
{
    private EntityManagerInterface $entityManager;
    private VerificationRepository $verificationRepository;
    private int $verificationCodeLength;

    public function __construct(
        EntityManagerInterface $entityManager,
        VerificationRepository $verificationRepository,
        int $verificationCodeLength
    ) {
        $this->entityManager = $entityManager;
        $this->verificationRepository = $verificationRepository;
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

        // here need to validate entity before actually persisting

        $this->entityManager->persist($verification);
        $this->entityManager->flush();
        $this->entityManager->refresh($verification);

        return new VerificationCreationResponse($verification->getId()?->toString());
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
