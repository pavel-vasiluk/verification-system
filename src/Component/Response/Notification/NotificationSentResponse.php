<?php

declare(strict_types=1);

namespace App\Component\Response\Notification;

use App\Component\Response\AbstractResponse;
use JetBrains\PhpStorm\ArrayShape;

class NotificationSentResponse extends AbstractResponse
{
    private bool $isSuccessful;

    public function __construct(bool $isSuccessful)
    {
        $this->isSuccessful = $isSuccessful;
    }

    public function isSuccessful(): bool
    {
        return $this->isSuccessful;
    }

    #[ArrayShape(['isSuccessful' => 'string'])]
    public function jsonSerialize(): array
    {
        return [
            'isSuccessful' => $this->isSuccessful(),
        ];
    }
}
