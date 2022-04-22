<?php

declare(strict_types=1);

namespace App\Service;

use App\Component\DTO\Request\NotificationSubjectDTO;
use App\Entity\Notification;
use App\Enums\ConfirmationTypes;
use App\Enums\NotificationChannels;
use App\Enums\TemplateSlug;
use App\Exception\NotificationSubjectException;
use App\HttpClient\VerificationHttpClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NotificationService
{
    private const CONFIRMATION_TYPE_SLUG = [
        ConfirmationTypes::EMAIL_CONFIRMATION => TemplateSlug::EMAIL_VERIFICATION,
        ConfirmationTypes::MOBILE_CONFORMATION => TemplateSlug::MOBILE_VERIFICATION,
    ];

    private EntityManagerInterface $entityManager;
    private VerificationHttpClient $verificationHttpClient;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        VerificationHttpClient $verificationHttpClient,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->verificationHttpClient = $verificationHttpClient;
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

        // TODO: dispatch NotificationCreated event
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
