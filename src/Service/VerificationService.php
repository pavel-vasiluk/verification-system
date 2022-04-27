<?php

declare(strict_types=1);

namespace App\Service;

use App\Component\Request\Verification\VerificationConfirmationRequest;
use App\Component\Request\Verification\VerificationCreationRequest;
use App\Component\Response\Verification\VerificationCreationResponse;
use App\Entity\Verification;
use App\Event\Verification\VerificationCreatedEvent;
use App\Exception\DuplicatedVerificationException;
use App\Helper\VerificationCodeGenerationHelper;
use App\Middleware\Verification\Confirmation\Handler\ConfirmationHandlerInterface;
use App\Repository\VerificationRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

class VerificationService
{
    private EntityManagerInterface $entityManager;
    private VerificationRepository $verificationRepository;
    private ConfirmationHandlerInterface $confirmationHandler;
    private EventDispatcherInterface $eventDispatcher;
    private int $verificationCodeLength;

    public function __construct(
        EntityManagerInterface $entityManager,
        VerificationRepository $verificationRepository,
        ConfirmationHandlerInterface $confirmationHandler,
        EventDispatcherInterface $eventDispatcher,
        int $verificationCodeLength,
    ) {
        $this->entityManager = $entityManager;
        $this->verificationRepository = $verificationRepository;
        $this->confirmationHandler = $confirmationHandler;
        $this->eventDispatcher = $eventDispatcher;
        $this->verificationCodeLength = $verificationCodeLength;
    }

    /**
     * @throws DuplicatedVerificationException
     */
    public function createVerification(VerificationCreationRequest $request): VerificationCreationResponse
    {
        if ($this->verificationRepository->findIdsBySubject($request->getSubject())) {
            throw new DuplicatedVerificationException();
        }

        $verification = (new Verification())
            ->setCode(VerificationCodeGenerationHelper::generateVerificationCode($this->verificationCodeLength))
            ->setSubject($request->getSubject()->jsonSerialize())
            ->setUserInfo($request->getUserInfo()->jsonSerialize())
        ;

        $this->entityManager->persist($verification);
        $this->entityManager->flush();
        $this->entityManager->refresh($verification);

        $this->eventDispatcher->dispatch(
            new VerificationCreatedEvent(
                $verification->getId()?->toString(),
                Response::HTTP_CREATED,
                $verification->getSubject(),
                Carbon::now(),
            )
        );

        return new VerificationCreationResponse($verification->getId()?->toString());
    }

    public function confirmVerification(VerificationConfirmationRequest $request): void
    {
        $this->confirmationHandler->process($request);
    }
}
