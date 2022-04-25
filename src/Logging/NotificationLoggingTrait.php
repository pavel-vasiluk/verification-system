<?php

declare(strict_types=1);

namespace App\Logging;

use App\Component\DTO\Messenger\NotificationMessageDTO;
use JsonException;

trait NotificationLoggingTrait
{
    /**
     * @throws JsonException
     */
    private function logSuccessfullySentNotification(NotificationMessageDTO $notificationMessage): void
    {
        $this->logger->info(
            sprintf(
                'Notification message %s was successfully sent. Message payload: %s.',
                $notificationMessage->getId(),
                json_encode($notificationMessage, JSON_THROW_ON_ERROR),
            )
        );
    }

    /**
     * @throws JsonException
     */
    private function logNotificationSendingFailure(
        NotificationMessageDTO $notificationMessage,
        string $errorResponse
    ): void {
        $this->logger->error(
            sprintf(
                'Notification message %s was not send. Message payload: %s. Error response: %s.',
                $notificationMessage->getId(),
                json_encode($notificationMessage, JSON_THROW_ON_ERROR),
                $errorResponse,
            )
        );
    }
}
