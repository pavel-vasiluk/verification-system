<?php

declare(strict_types=1);

namespace App\Client\Http;

use App\Client\NotificationClientInterface;
use App\Component\DTO\Messenger\NotificationMessageDTO;
use App\Component\Response\Notification\NotificationSentResponse;
use App\Enums\NotificationChannels;
use App\Helper\NotificationLoggingHelper;
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
    public function sendNotification(NotificationMessageDTO $notificationMessage): NotificationSentResponse
    {
        $httpResponse = $this->httpClient->request(
            Request::METHOD_POST,
            sprintf('/message?token=%s', $this->token),
            [
                'body' => [
                    'title' => $notificationMessage->getRecipient(),
                    'message' => $notificationMessage->getBody(),
                ],
            ],
        );

        $notificationSentResponse = new NotificationSentResponse(
            Response::HTTP_OK === $httpResponse->getStatusCode()
        );

        match ($notificationSentResponse->isSuccessful()) {
            true => NotificationLoggingHelper::logSuccessfullySentNotification(
                $this->logger,
                $notificationMessage
            ),
            false => NotificationLoggingHelper::logNotificationSendingFailure(
                $this->logger,
                $notificationMessage,
                $httpResponse->getContent(false)
            ),
        };

        return $notificationSentResponse;
    }
}
