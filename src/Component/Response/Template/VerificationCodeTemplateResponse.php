<?php

declare(strict_types=1);

namespace App\Component\Response\Template;

use App\Component\Response\AbstractResponse;
use JetBrains\PhpStorm\ArrayShape;

class VerificationCodeTemplateResponse extends AbstractResponse
{
    private string $template;
    private string $code;

    public function __construct(string $template, string $code)
    {
        $this->template = $template;
        $this->code = $code;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    #[ArrayShape(['template' => 'string', 'code' => 'string'])]
    public function jsonSerialize(): array
    {
        return [
            'template' => $this->getTemplate(),
            'code' => $this->getCode(),
        ];
    }
}
