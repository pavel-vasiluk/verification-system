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

    public function __construct(
        EntityManagerInterface $entityManager,
        VerificationRepository $verificationRepository
    ) {
        $this->entityManager = $entityManager;
        $this->verificationRepository = $verificationRepository;
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
            ->setCode('12345678')
            ->setSubject($request->getSubject()->jsonSerialize())
            ->setUserInfo($request->getUserInfo()->jsonSerialize())
        ;

        // here need to validate entity before actually persisting

        $this->entityManager->persist($verification);
        $this->entityManager->flush();
        $this->entityManager->refresh($verification);

        return new VerificationCreationResponse($verification->getId()?->toString());
    }
}
