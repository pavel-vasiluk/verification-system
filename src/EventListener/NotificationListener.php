<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\Notification\NotificationCreatedEvent;
use App\Event\Notification\NotificationDispatchedEvent;
use App\Exception\NotificationNotFoundException;
use App\Helper\NotificationLoggingHelper;
use App\Message\Notification\NotificationCreatedMessage;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use JsonException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class NotificationListener implements EventSubscriberInterface
{
    private MessageBusInterface $messageBus;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private NotificationRepository $notificationRepository;

    public function __construct(
        MessageBusInterface $messageBus,
        EntityManagerInterface $entityManager,
        LoggerInterface $notificationLogger,
        NotificationRepository $notificationRepository
    ) {
        $this->messageBus = $messageBus;
        $this->entityManager = $entityManager;
        $this->logger = $notificationLogger;
        $this->notificationRepository = $notificationRepository;
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
        NotificationLoggingHelper::logNotificationCreatedEvent($this->logger, $event);

        $this->messageBus->dispatch(
            new NotificationCreatedMessage($event->getId())
        );
    }

    /**
     * @throws NotificationNotFoundException
     */
    public function onNotificationDispatched(NotificationDispatchedEvent $event): void
    {
        if (!$notification = $this->notificationRepository->find($event->getId())) {
            throw new NotificationNotFoundException();
        }

        $notification->setDispatched(true);
        $this->entityManager->flush();

        NotificationLoggingHelper::logNotificationDispatchedEvent($this->logger, $event);
    }
}
