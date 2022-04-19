<?php

declare(strict_types=1);

namespace App\Component\DTO\Request;

use App\Component\DTO\AbstractDTO;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;

class VerificationSubjectDTO extends AbstractDTO implements JsonSerializable
{
    #[Assert\NotNull]
    #[Assert\Type(type: 'string')]
    protected $identity;

    #[Assert\NotNull]
    #[Assert\Type(type: 'string')]
    protected $type;

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
