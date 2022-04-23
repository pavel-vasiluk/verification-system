<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\Notification\NotificationCreatedEvent;
use App\Event\Notification\NotificationDispatchedEvent;
use App\Message\Verification\NotificationCreatedMessage;
use JetBrains\PhpStorm\ArrayShape;
use JsonException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class NotificationListener implements EventSubscriberInterface
{
    private MessageBusInterface $messageBus;
    private LoggerInterface $logger;

    public function __construct(
        MessageBusInterface $messageBus,
        LoggerInterface $notificationLogger
    ) {
        $this->messageBus = $messageBus;
        $this->logger = $notificationLogger;
    }

    #[ArrayShape(
        [
            NotificationCreatedEvent::class => 'string',
            NotificationDispatchedEvent::class => 'string',
        ]
    )]
    public static function getSubscribedEvents(): array
    {
        return [
            NotificationCreatedEvent::class => 'onNotificationCreated',
            NotificationDispatchedEvent::class => 'onNotificationDispatched',
        ];
    }

    /**
     * @throws JsonException
     */
    public function onNotificationCreated(NotificationCreatedEvent $event): void
    {
        $this->logger->info(
            sprintf(
                'Notification %s has been created. Event payload: %s',
                $event->getId(),
                json_encode($event, JSON_THROW_ON_ERROR)
            )
        );

        $this->messageBus->dispatch(
            new NotificationCreatedMessage($event->getId())
        );
    }

    public function onNotificationDispatched(NotificationDispatchedEvent $event): void
    {
    }
}
