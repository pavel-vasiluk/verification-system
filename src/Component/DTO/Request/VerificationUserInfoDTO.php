<?php

declare(strict_types=1);

namespace App\Component\DTO\Request;

use App\Component\DTO\AbstractDTO;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;

class VerificationUserInfoDTO extends AbstractDTO implements JsonSerializable
{
    #[Assert\NotNull]
    #[Assert\Type(type: 'string')]
    protected $clientIp;

    #[Assert\NotNull]
    #[Assert\Type(type: 'string')]
    protected $userAgent;

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
