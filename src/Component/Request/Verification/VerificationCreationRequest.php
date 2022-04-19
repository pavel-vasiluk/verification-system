<?php

declare(strict_types=1);

namespace App\Component\Request\Template;

use App\Component\Request\AbstractJsonBodyRequest;
use Symfony\Component\Validator\Constraints as Assert;

class VerificationCreationRequest extends AbstractJsonBodyRequest
{
    protected const EXCEPTION = 'Validation failed: invalid subject supplied.';

    /** @var string */
    #[Assert\NotNull]
    #[Assert\Type(type: 'string')]
    protected $identity;

    /** @var string */
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
}
