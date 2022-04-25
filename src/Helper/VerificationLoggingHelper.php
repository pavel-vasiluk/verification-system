<?php

declare(strict_types=1);

namespace App\Helper;

use App\Event\Verification\VerificationConfirmationFailedEvent;
use App\Event\Verification\VerificationConfirmedEvent;
use App\Event\Verification\VerificationCreatedEvent;
use JsonException;
use Psr\Log\LoggerInterface;

final class VerificationLoggingHelper
{
    /**
     * @throws JsonException
     */
    public static function logVerificationCreatedEvent(
        LoggerInterface $logger,
        VerificationCreatedEvent $event
    ): void {
        $logger->info(
            sprintf(
                'Verification %s has been created. Event payload: %s',
                $event->getId(),
                json_encode($event, JSON_THROW_ON_ERROR)
            )
        );
    }

    /**
     * @throws JsonException
     */
    public static function logVerificationConfirmedEvent(
        LoggerInterface $logger,
        VerificationConfirmedEvent $event,
    ): void {
        $logger->info(
            sprintf(
                'Verification %s has been successfully confirmed. Event payload: %s',
                $event->getId(),
                json_encode($event, JSON_THROW_ON_ERROR)
            )
        );
    }

    /**
     * @throws JsonException
     */
    public static function logVerificationConfirmationFailedEvent(
        LoggerInterface $logger,
        VerificationConfirmationFailedEvent $event,
    ): void {
        $logger->error(
            sprintf(
                'Verification confirmation failure report. Event payload: %s',
                json_encode($event, JSON_THROW_ON_ERROR)
            )
        );
    }
}
