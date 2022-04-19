<?php

declare(strict_types=1);

namespace App\Service;

use App\Component\DTO\Request\VerificationSubjectDTO;
use App\Component\DTO\Request\VerificationUserInfoDTO;
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
        $httpRequest = $request->getRequest();
        $userInfo = new VerificationUserInfoDTO(
            $httpRequest->getClientIp() ?? '',
            $httpRequest->headers->get('User-Agent') ?? ''
        );
        $subject = new VerificationSubjectDTO($request->getIdentity(), $request->getType());

        $verification = (new Verification())
            ->setCode('12345678')
            ->setSubject($subject->jsonSerialize())
            ->setUserInfo($userInfo->jsonSerialize())
        ;

        // here need to validate entity before actually persisting

        $this->entityManager->persist($verification);
        $this->entityManager->flush();
        $this->entityManager->refresh($verification);

        return $verification->getId()?->toString();
    }
}
