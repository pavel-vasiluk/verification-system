<?php

declare(strict_types=1);

namespace App\Service;

use App\Component\Request\Template\VerificationCreationRequest;
use App\Entity\Verification;
use Doctrine\ORM\EntityManagerInterface;

class VerificationService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createVerification(VerificationCreationRequest $request): string
    {
        $verification = (new Verification())
            ->setCode('12345678')
            ->setSubject($request->getSubject()->jsonSerialize())
            ->setUserInfo($request->getUserInfo()->jsonSerialize())
        ;

        // here need to validate entity before actually persisting

        $this->entityManager->persist($verification);
        $this->entityManager->flush();
        $this->entityManager->refresh($verification);

        return $verification->getId()?->toString();
    }
}
