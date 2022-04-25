<?php

declare(strict_types=1);

namespace App\Resolver;

use App\Client\NotificationClientInterface;
use App\Component\DTO\Messenger\NotificationMessageDTO;
use App\Exception\NotificationClientNotFoundException;

class NotificationClientResolver
{
    /** @var NotificationClientInterface[] */
    private iterable $clients;

    public function __construct(iterable $clients)
    {
        $this->clients = $clients;
    }

    /**
     * @throws NotificationClientNotFoundException
     */
    public function resolve(NotificationMessageDTO $notificationMessageDTO): NotificationClientInterface
    {
        foreach ($this->clients as $client) {
            if ($client->supports($notificationMessageDTO)) {
                return $client;
            }
        }

        throw new NotificationClientNotFoundException();
    }
}
