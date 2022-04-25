<?php

declare(strict_types=1);

namespace App\Service;

use App\Client\Http\VerificationHttpClient;
use App\Component\DTO\Messenger\NotificationMessageDTO;
use App\Component\DTO\Messenger\NotificationSubjectDTO;
use App\Entity\Notification;
use App\Enums\ConfirmationTypes;
use App\Enums\NotificationChannels;
use App\Enums\TemplateSlug;
use App\Event\Notification\NotificationCreatedEvent;
use App\Exception\NotificationMessageException;
use App\Exception\NotificationSubjectException;
use App\Resolver\NotificationClientResolver;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NotificationService
{
    private const CONFIRMATION_TYPE_SLUG = [
        ConfirmationTypes::EMAIL_CONFIRMATION => TemplateSlug::EMAIL_VERIFICATION,
        ConfirmationTypes::MOBILE_CONFORMATION => TemplateSlug::MOBILE_VERIFICATION,
    ];

    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $eventDispatcher;
    private VerificationHttpClient $verificationHttpClient;
    private NotificationClientResolver $notificationClientResolver;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        VerificationHttpClient $verificationHttpClient,
        NotificationClientResolver $notificationClientResolver,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->verificationHttpClient = $verificationHttpClient;
        $this->notificationClientResolver = $notificationClientResolver;
        $this->validator = $validator;
    }

    /**
     * @throws NotificationSubjectException
     */
    public function createNotification(NotificationSubjectDTO $notificationSubject): void
    {
        if (count($this->validator->validate($notificationSubject)) > 0) {
            throw new NotificationSubjectException();
        }

        $notification = (new Notification())
            ->setRecipient($notificationSubject->getIdentity())
            ->setChannel($this->resolveNotificationChannel($notificationSubject))
            ->setBody($this->resolveNotificationBody($notificationSubject))
        ;

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
        $this->entityManager->refresh($notification);

        $this->eventDispatcher->dispatch(
            new NotificationCreatedEvent($notification->getId()?->toString(), Carbon::now())
        );
    }

    /**
     * @throws NotificationMessageException
     */
    public function sendNotification(NotificationMessageDTO $notificationMessage): void
    {
        if (count($this->validator->validate($notificationMessage)) > 0) {
            throw new NotificationMessageException();
        }

        $notificationClient = $this->notificationClientResolver->resolve($notificationMessage);
        $notificationClient->sendNotification($notificationMessage);
    }

    private function resolveNotificationBody(NotificationSubjectDTO $notificationSubject): string
    {
        $slug = self::CONFIRMATION_TYPE_SLUG[$notificationSubject->getType()];
        $variables = ['code' => $notificationSubject->getCode()];

        return $this->verificationHttpClient
            ->requestNotificationTemplate($slug, $variables)
            ->getContent()
        ;
    }

    private function resolveNotificationChannel(NotificationSubjectDTO $notificationSubject): string
    {
        return match ($notificationSubject->getType()) {
            ConfirmationTypes::EMAIL_CONFIRMATION => NotificationChannels::EMAIL_CHANNEL,
            ConfirmationTypes::MOBILE_CONFORMATION => NotificationChannels::SMS_CHANNEL,
        };
    }
}
