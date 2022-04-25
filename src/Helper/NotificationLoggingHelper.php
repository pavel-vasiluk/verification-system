<?php

declare(strict_types=1);

namespace App\Helper;

use App\Component\DTO\Messenger\NotificationMessageDTO;
use App\Event\Notification\NotificationCreatedEvent;
use JsonException;
use Psr\Log\LoggerInterface;

final class NotificationLoggingHelper
{
    /**
     * @throws JsonException
     */
    public static function logNotificationCreatedEvent(
        LoggerInterface $logger,
        NotificationCreatedEvent $event
    ): void {
        $logger->info(
            sprintf(
                'Notification %s has been created. Event payload: %s',
                $event->getId(),
                json_encode($event, JSON_THROW_ON_ERROR)
            )
        );
    }

    /**
     * @throws JsonException
     */
    public static function logSuccessfullySentNotification(
        LoggerInterface $logger,
        NotificationMessageDTO $notificationMessage
    ): void {
        $logger->info(
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
    public static function logNotificationSendingFailure(
        LoggerInterface $logger,
        NotificationMessageDTO $notificationMessage,
        string $errorResponse
    ): void {
        $logger->error(
            sprintf(
                'Notification message %s was not send. Message payload: %s. Error response: %s.',
                $notificationMessage->getId(),
                json_encode($notificationMessage, JSON_THROW_ON_ERROR),
                $errorResponse,
            )
        );
    }
}
