<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\Verification\VerificationConfirmationFailedEvent;
use App\Event\Verification\VerificationConfirmedEvent;
use App\Event\Verification\VerificationCreatedEvent;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class VerificationListener implements EventSubscriberInterface
{
    #[ArrayShape(
        [
            VerificationCreatedEvent::class => 'string',
            VerificationConfirmedEvent::class => 'string',
            VerificationConfirmationFailedEvent::class => 'string',
        ]
    )]
    public static function getSubscribedEvents(): array
    {
        return [
            VerificationCreatedEvent::class => 'onVerificationCreated',
            VerificationConfirmedEvent::class => 'onVerificationConfirmed',
            VerificationConfirmationFailedEvent::class => 'onVerificationConfirmationFailed',
        ];
    }

    public function onVerificationCreated(VerificationCreatedEvent $event): void
    {
    }

    public function onVerificationConfirmed(VerificationConfirmedEvent $event): void
    {
    }

    public function onVerificationConfirmationFailed(VerificationConfirmationFailedEvent $event): void
    {
    }
}
