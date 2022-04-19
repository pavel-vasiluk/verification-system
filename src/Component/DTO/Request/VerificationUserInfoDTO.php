<?php

declare(strict_types=1);

namespace App\Component\DTO\Request;

use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;

class VerificationUserInfoDTO implements JsonSerializable
{
    private string $clientIp;
    private string $userAgent;

    public function __construct(string $clientIp, string $userAgent)
    {
        $this->clientIp = $clientIp;
        $this->userAgent = $userAgent;
    }

    public function getClientIp(): string
    {
        return $this->clientIp;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    #[ArrayShape(['clientIp' => 'string', 'userAgent' => 'string'])]
    public function jsonSerialize(): array
    {
        return [
            'clientIp' => $this->getClientIp(),
            'userAgent' => $this->getUserAgent(),
        ];
    }
}
