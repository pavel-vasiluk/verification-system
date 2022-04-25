<?php

declare(strict_types=1);

namespace App\MessageHandler\Verification;

use App\Component\DTO\Messenger\NotificationSubjectDTO;
use App\Exception\VerificationNotFoundException;
use App\Message\Verification\VerificationCreatedMessage;
use App\Repository\VerificationRepository;
use App\Service\NotificationService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class VerificationCreatedMessageHandler
{
    protected VerificationRepository $verificationRepository;
    protected NotificationService $notificationService;

    public function __construct(
        VerificationRepository $verificationRepository,
        NotificationService $notificationService,
    ) {
        $this->verificationRepository = $verificationRepository;
        $this->notificationService = $notificationService;
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
