<?php

declare(strict_types=1);

namespace App\Client;

use App\Component\DTO\Messenger\NotificationMessageDTO;

interface NotificationClientInterface
{
    public function supports(NotificationMessageDTO $notificationMessage): bool;

    public function sendNotification(NotificationMessageDTO $notificationMessage): void;
}
