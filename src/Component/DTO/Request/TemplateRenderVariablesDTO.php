<?php

declare(strict_types=1);

namespace App\Component\DTO\Request;

use App\Component\DTO\AbstractDTO;
use Symfony\Component\Validator\Constraints as Assert;

class TemplateRenderVariablesDTO extends AbstractDTO
{
    #[Assert\NotNull]
    #[Assert\Type(type: 'string')]
    #[Assert\Type(type: 'digit')]
    protected $code;

    public function getCode(): string
    {
        return $this->code;
    }
}
