<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\Verification\VerificationConfirmationFailedEvent;
use App\Event\Verification\VerificationConfirmedEvent;
use App\Event\Verification\VerificationCreatedEvent;
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
        $this->logEvent(
            sprintf(
                'Verification %s has been created. Event payload: %s',
                $event->getId(),
                json_encode($event, JSON_THROW_ON_ERROR)
            )
        );

        $message = new VerificationCreatedMessage(
            $event->getId(),
            $event->getCode(),
            $event->getSubject(),
            $event->getOccurredOn(),
        );

        $this->messageBus->dispatch($message);
    }

    /**
     * @throws JsonException
     */
    public function onVerificationConfirmed(VerificationConfirmedEvent $event): void
    {
        $this->logEvent(
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
    public function onVerificationConfirmationFailed(VerificationConfirmationFailedEvent $event): void
    {
        $this->logEvent(
            sprintf(
                'Verification confirmation failure report. Event payload: %s',
                json_encode($event, JSON_THROW_ON_ERROR)
            )
        );
    }

    private function logEvent(string $message): void
    {
        $this->logger->info($message);
    }
}
