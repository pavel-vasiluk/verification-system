<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\Verification\VerificationConfirmationFailedEvent;
use App\Event\Verification\VerificationConfirmedEvent;
use App\Event\Verification\VerificationCreatedEvent;
use App\Helper\VerificationLoggingHelper;
use App\Message\Verification\VerificationCreatedMessage;
use JetBrains\PhpStorm\ArrayShape;
use JsonException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class VerificationListener implements EventSubscriberInterface
{
    private MessageBusInterface $messageBus;
    private LoggerInterface $logger;

    public function __construct(
        MessageBusInterface $messageBus,
        LoggerInterface $verificationLogger
    ) {
        $this->messageBus = $messageBus;
        $this->logger = $verificationLogger;
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

    /**
     * @throws JsonException
     */
    public function onVerificationCreated(VerificationCreatedEvent $event): void
    {
        VerificationLoggingHelper::logVerificationCreatedEvent($this->logger, $event);

        $this->messageBus->dispatch(
            new VerificationCreatedMessage($event->getId())
        );
    }

    /**
     * @throws JsonException
     */
    public function onVerificationConfirmed(VerificationConfirmedEvent $event): void
    {
        VerificationLoggingHelper::logVerificationConfirmedEvent($this->logger, $event);
    }

    /**
     * @throws JsonException
     */
    public function onVerificationConfirmationFailed(VerificationConfirmationFailedEvent $event): void
    {
        VerificationLoggingHelper::logVerificationConfirmationFailedEvent($this->logger, $event);
    }
}
