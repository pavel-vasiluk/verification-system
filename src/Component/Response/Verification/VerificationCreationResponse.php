<?php

declare(strict_types=1);

namespace App\Component\Response\Verification;

use App\Component\Response\AbstractResponse;
use JetBrains\PhpStorm\ArrayShape;

class VerificationCreationResponse extends AbstractResponse
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
