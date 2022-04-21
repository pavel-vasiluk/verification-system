<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\Verification\VerificationConfirmationFailedEvent;
use App\Event\Verification\VerificationConfirmedEvent;
use App\Event\Verification\VerificationCreatedEvent;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class VerificationListener implements EventSubscriberInterface
{
    #[ArrayShape(
        [
            VerificationCreatedEvent::NAME => 'string',
            VerificationConfirmedEvent::NAME => 'string',
            VerificationConfirmationFailedEvent::NAME => 'string',
        ]
    )]
    public static function getSubscribedEvents(): array
    {
        return [
            VerificationCreatedEvent::NAME => 'onVerificationCreated',
            VerificationConfirmedEvent::NAME => 'onVerificationConfirmed',
            VerificationConfirmationFailedEvent::NAME => 'onVerificationConfirmationFailed',
        ];
    }

    public function onVerificationCreated(ExceptionEvent $event): void
    {
    }

    public function onVerificationConfirmed(ExceptionEvent $event): void
    {
    }

    public function onVerificationConfirmationFailed(ExceptionEvent $event): void
    {
    }
}
