<?php

declare(strict_types=1);

namespace App\Component\DTO\Request;

use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;

class VerificationSubjectDTO implements JsonSerializable
{
    private string $identity;
    private string $type;

    public function __construct(string $identity, string $type)
    {
        $this->identity = $identity;
        $this->type = $type;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }

    public function getType(): string
    {
        return $this->type;
    }

    #[ArrayShape(['identity' => 'string', 'type' => 'string'])]
    public function jsonSerialize(): array
    {
        return [
            'identity' => $this->getIdentity(),
            'type' => $this->getType(),
        ];
    }
}
