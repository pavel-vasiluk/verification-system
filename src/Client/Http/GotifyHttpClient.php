<?php

declare(strict_types=1);

namespace App\Client\Http;

use App\Client\NotificationClientInterface;
use App\Component\DTO\Messenger\NotificationMessageDTO;
use App\Enums\NotificationChannels;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GotifyHttpClient extends AbstractHttpClient implements NotificationClientInterface
{
    private string $token;

    public function __construct(
        HttpClientInterface $httpClient,
        string $baseUri,
        string $token
    ) {
        parent::__construct($httpClient, $baseUri);
        $this->token = $token;
    }

    public function supports(NotificationMessageDTO $notificationMessage): bool
    {
        return NotificationChannels::SMS_CHANNEL === $notificationMessage->getChannel();
    }

    public function sendNotification(NotificationMessageDTO $notificationMessage): void
    {
        $this->httpClient->request(
            Request::METHOD_POST,
            sprintf('/message?token=%s', $this->token),
            [
                'body' => [
                    'title' => $notificationMessage->getRecipient(),
                    'message' => $notificationMessage->getBody(),
                ],
            ],
        );
    }
}
