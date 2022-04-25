<?php

declare(strict_types=1);

namespace App\Client\Smtp;

use App\Client\NotificationClientInterface;
use App\Component\DTO\Messenger\NotificationMessageDTO;
use App\Enums\NotificationChannels;
use App\Logging\NotificationLoggingTrait;
use JsonException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Mime\Email;

class MailhogSmtpClient implements NotificationClientInterface
{
    use NotificationLoggingTrait;

    private MailerInterface $mailer;
    private LoggerInterface $logger;

    public function __construct(MailerInterface $mailer, LoggerInterface $notificationLogger)
    {
        $this->mailer = $mailer;
        $this->logger = $notificationLogger;
    }

    public function supports(NotificationMessageDTO $notificationMessage): bool
    {
        return NotificationChannels::EMAIL_CHANNEL === $notificationMessage->getChannel();
    }

    /**
     * @throws JsonException
     */
    public function sendNotification(NotificationMessageDTO $notificationMessage): void
    {
        $email = (new Email())
            ->from('info@dev.verification.eu')
            ->to($notificationMessage->getRecipient())
            ->subject('Verification Code')
            ->html($notificationMessage->getBody())
        ;

        try {
            $this->mailer->send($email);
        } catch (HandlerFailedException|TransportExceptionInterface $e) {
            $this->logNotificationSendingFailure($notificationMessage, $e->getMessage());

            return;
        }

        $this->logSuccessfullySentNotification($notificationMessage);
    }
}
