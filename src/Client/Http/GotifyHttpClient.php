<?php

declare(strict_types=1);

namespace App\Client\Http;

use App\Client\NotificationClientInterface;
use App\Component\DTO\Messenger\NotificationMessageDTO;
use App\Enums\NotificationChannels;
use JsonException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GotifyHttpClient extends AbstractHttpClient implements NotificationClientInterface
{
    private LoggerInterface $logger;
    private string $token;

    public function __construct(
        HttpClientInterface $httpClient,
        LoggerInterface $notificationLogger,
        string $baseUri,
        string $token
    ) {
        parent::__construct($httpClient, $baseUri);
        $this->logger = $notificationLogger;
        $this->token = $token;
    }

    public function supports(NotificationMessageDTO $notificationMessage): bool
    {
        return NotificationChannels::SMS_CHANNEL === $notificationMessage->getChannel();
    }

    /**
     * @throws JsonException
     */
    public function sendNotification(NotificationMessageDTO $notificationMessage): void
    {
        $response = $this->httpClient->request(
            Request::METHOD_POST,
            sprintf('/message?token=%s', $this->token),
            [
                'body' => [
                    'title' => $notificationMessage->getRecipient(),
                    'message' => $notificationMessage->getBody(),
                ],
            ],
        );

        $messagePayload = json_encode($notificationMessage, JSON_THROW_ON_ERROR);

        match ($response->getStatusCode()) {
            Response::HTTP_OK => $this->logger->info(
                sprintf(
                    'Notification message %s was successfully sent. Message payload: %s.',
                    $notificationMessage->getId(),
                    $messagePayload,
                )
            ),
            default => $this->logger->error(
                sprintf(
                    'Notification message %s was not send. Message payload: %s. Error response: %s.',
                    $notificationMessage->getId(),
                    $messagePayload,
                    $response->getContent(false),
                )
            ),
        };
    }
}
