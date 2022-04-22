<?php

declare(strict_types=1);

namespace App\MessageHandler\Verification;

use App\Component\DTO\Request\NotificationSubjectDTO;
use App\Exception\VerificationNotFoundException;
use App\Message\Verification\VerificationCreatedMessage;
use App\Repository\VerificationRepository;
use App\Service\NotificationService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class VerificationCreatedMessageHandler
{
    protected VerificationRepository $verificationRepository;
    protected NotificationService $notificationService;
    protected LoggerInterface $logger;

    public function __construct(
        VerificationRepository $verificationRepository,
        NotificationService $notificationService,
        LoggerInterface $verificationLogger
    ) {
        $this->verificationRepository = $verificationRepository;
        $this->notificationService = $notificationService;
        $this->logger = $verificationLogger;
    }

    /**
     * @throws VerificationNotFoundException
     */
    public function __invoke(VerificationCreatedMessage $message): void
    {
        if (!$verification = $this->verificationRepository->find($message->getId())) {
            throw new VerificationNotFoundException();
        }

        $notificationSubject = new NotificationSubjectDTO(
            array_merge($verification->getSubject(), ['code' => $verification->getCode()])
        );
        $this->notificationService->createNotification($notificationSubject);
    }
}
