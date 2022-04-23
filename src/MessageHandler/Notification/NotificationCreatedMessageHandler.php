<?php

declare(strict_types=1);

namespace App\MessageHandler\Notification;

use App\Message\Verification\NotificationCreatedMessage;
use App\Service\NotificationService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NotificationCreatedMessageHandler
{
    protected NotificationService $notificationService;

    public function __construct(
        NotificationService $notificationService,
    ) {
        $this->notificationService = $notificationService;
    }

    public function __invoke(NotificationCreatedMessage $message): void
    {
    }
}
