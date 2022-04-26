<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Component\DTO\Messenger\NotificationSubjectDTO;
use App\Entity\Notification;
use App\Enums\ConfirmationTypes;
use App\Enums\NotificationChannels;
use App\Exception\NotificationSubjectException;
use App\Helper\VerificationCodeGenerationHelper;
use App\Message\Notification\NotificationCreatedMessage;
use App\Repository\NotificationRepository;
use App\Service\NotificationService;
use App\Tests\AbstractWebTestCase;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @covers \App\Service\NotificationService
 *
 * @internal
 */
class NotificationServiceTest extends AbstractWebTestCase
{
    private NotificationService $notificationService;
    private NotificationRepository $notificationRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->notificationService = $this->container->get(NotificationService::class);
        $this->notificationRepository = $this->container->get(NotificationRepository::class);

        $this->truncateEntities([
            Notification::class,
        ]);
    }

    /**
     * @dataProvider notificationCreationDataProvider
     */
    public function testNotificationCreated(
        string $identity,
        string $type,
        string $expectedNotificationChannel
    ): void {
        $this->assertEmpty($this->messageTransport->get());
        $this->assertNull($this->notificationRepository->findOneBy(['recipient' => $identity]));

        $verificationCode = VerificationCodeGenerationHelper::generateVerificationCode(8);
        $notificationSubject = new NotificationSubjectDTO([
            'identity' => $identity,
            'type' => $type,
            'code' => $verificationCode,
        ]);
        $this->notificationService->createNotification($notificationSubject);

        $notification = $this->notificationRepository->findOneBy(['recipient' => $identity]);

        $this->assertNotNull($notification);
        $this->assertStringContainsString($verificationCode, $notification->getBody());
        $this->assertSame($identity, $notification->getRecipient());
        $this->assertSame($expectedNotificationChannel, $notification->getChannel());

        $envelopes = $this->messageTransport->get();
        $this->assertCount(1, $envelopes);

        $envelopeMessage = $envelopes[0]->getMessage();
        $this->assertInstanceOf(NotificationCreatedMessage::class, $envelopeMessage);
        $this->assertSame($notification->getId()->toString(), $envelopeMessage->getId());
    }

    /**
     * @dataProvider invalidNotificationSubjectValidationDataProvider
     */
    public function testNotificationSubjectValidationFailed(mixed $identity, mixed $type, mixed $code): void
    {
        $this->expectException(NotificationSubjectException::class);

        $notificationSubject = new NotificationSubjectDTO([
            'identity' => $identity,
            'type' => $type,
            'code' => $code,
        ]);
        $this->notificationService->createNotification($notificationSubject);
    }

    #[ArrayShape(['email notification' => 'array', 'sms notification' => 'array'])]
    public function notificationCreationDataProvider(): array
    {
        return [
            'email notification' => [
                'john.doe@abc.xyz',
                ConfirmationTypes::EMAIL_CONFIRMATION,
                NotificationChannels::EMAIL_CHANNEL,
            ],
            'sms notification' => [
                '+37120000001',
                ConfirmationTypes::MOBILE_CONFORMATION,
                NotificationChannels::SMS_CHANNEL,
            ],
        ];
    }

    public function invalidNotificationSubjectValidationDataProvider(): array
    {
        $correctEmailIdentity = 'john.doe@abc.xyz';
        $correctMobileIdentity = '+37120000001';
        $correctEmailType = ConfirmationTypes::EMAIL_CONFIRMATION;
        $correctMobileType = ConfirmationTypes::MOBILE_CONFORMATION;
        $correctVerificationCode = VerificationCodeGenerationHelper::generateVerificationCode(8);

        return [
            'nullable verification code' => [
                $correctEmailIdentity,
                $correctEmailType,
                null,
            ],
            'invalid verification code type' => [
                $correctEmailIdentity,
                $correctEmailType,
                (int) $correctVerificationCode,
            ],
            'non-digits verification code' => [
                $correctEmailIdentity,
                $correctEmailType,
                uniqid('', true),
            ],
            'nullable notification type' => [
                $correctEmailIdentity,
                null,
                $correctVerificationCode,
            ],
            'non-string notification type' => [
                $correctEmailIdentity,
                true,
                $correctVerificationCode,
            ],
            'notification type of non-existing choice' => [
                $correctEmailIdentity,
                '2fa_confirmation',
                $correctVerificationCode,
            ],
            'nullable notification identity' => [
                null,
                $correctEmailType,
                $correctVerificationCode,
            ],
            'non-string notification identity' => [
                true,
                $correctEmailType,
                $correctVerificationCode,
            ],
            'invalid email identity' => [
                $correctMobileIdentity,
                $correctEmailType,
                $correctVerificationCode,
            ],
            'invalid mobile identity' => [
                $correctEmailIdentity,
                $correctMobileType,
                $correctVerificationCode,
            ],
        ];
    }
}
