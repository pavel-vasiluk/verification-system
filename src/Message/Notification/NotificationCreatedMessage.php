<?php

declare(strict_types=1);

namespace App\Message\Notification;

use App\Message\AbstractMessage;
use JetBrains\PhpStorm\ArrayShape;

class NotificationCreatedMessage extends AbstractMessage
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    #[ArrayShape(['id' => 'string'])]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
        ];
    }
}
