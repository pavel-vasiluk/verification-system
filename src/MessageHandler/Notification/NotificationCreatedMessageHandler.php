<?php

declare(strict_types=1);

namespace App\MessageHandler\Notification;

use App\Component\DTO\Messenger\NotificationMessageDTO;
use App\Exception\NotificationNotFoundException;
use App\Message\Verification\NotificationCreatedMessage;
use App\Repository\NotificationRepository;
use App\Service\NotificationService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NotificationCreatedMessageHandler
{
    protected NotificationService $notificationService;
    private NotificationRepository $notificationRepository;

    public function __construct(
        NotificationService $notificationService,
        NotificationRepository $notificationRepository
    ) {
        $this->notificationService = $notificationService;
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @throws NotificationNotFoundException
     */
    public function __invoke(NotificationCreatedMessage $message): void
    {
        if (!$notification = $this->notificationRepository->find($message->getId())) {
            throw new NotificationNotFoundException();
        }

        $notificationMessagePayload = [
            'id' => $notification->getId(),
            'recipient' => $notification->getRecipient(),
            'channel' => $notification->getChannel(),
            'body' => $notification->getBody(),
        ];

        $notificationMessage = new NotificationMessageDTO($notificationMessagePayload);
        $this->notificationService->sendNotification($notificationMessage);
    }
}
