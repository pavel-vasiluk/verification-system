<?php

declare(strict_types=1);

namespace App\Component\Request\Template;

use App\Component\Request\AbstractJsonBodyRequest;
use App\Component\Request\DTO\TemplateRenderVariablesDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class TemplateRenderRequest extends AbstractJsonBodyRequest
{
    /** @var string */
    #[Assert\Type(type: 'string')]
    protected $slug;

    /** @var TemplateRenderVariablesDTO */
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
