<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\Verification\VerificationConfirmationFailedEvent;
use App\Event\Verification\VerificationConfirmedEvent;
use App\Event\Verification\VerificationCreatedEvent;
use App\Message\Verification\VerificationConfirmationFailedMessage;
use App\Message\Verification\VerificationConfirmedMessage;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class VerificationListener implements EventSubscriberInterface
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

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
        $message = new VerificationConfirmedMessage(
            $event->getId(),
            $event->getCode(),
            $event->getSubject(),
            $event->getOccurredOn(),
        );

        $this->messageBus->dispatch($message);
    }

    public function onVerificationConfirmationFailed(VerificationConfirmationFailedEvent $event): void
    {
        $message = new VerificationConfirmationFailedMessage(
            $event->getId(),
            $event->getCode(),
            $event->getSubject(),
            $event->getOccurredOn(),
            $event->getReason(),
        );

        $this->messageBus->dispatch($message);
    }
}
