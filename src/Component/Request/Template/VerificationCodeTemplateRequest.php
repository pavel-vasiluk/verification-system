<?php

declare(strict_types=1);

namespace App\Component\Request\Template;

use App\Component\DTO\Request\TemplateRenderVariablesDTO;
use App\Component\Request\AbstractJsonBodyRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class VerificationCodeTemplateRequest extends AbstractJsonBodyRequest
{
    protected const EXCEPTION = 'Validation failed: invalid / missing variables supplied.';

    /** @var string */
    #[Assert\NotNull]
    #[Assert\Type(type: 'string')]
    protected $slug;

    /** @var TemplateRenderVariablesDTO */
    #[Assert\Valid]
    protected $variables;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->variables = new TemplateRenderVariablesDTO(
            is_array($this->variables) ? $this->variables : []
        );
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getVariables(): TemplateRenderVariablesDTO
    {
        return $this->variables;
    }
}
