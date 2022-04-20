<?php

declare(strict_types=1);

namespace App\Component\Request\Verification;

use App\Component\Request\AbstractUserInfoAwareRequest;
use Symfony\Component\Validator\Constraints as Assert;

class VerificationConfirmationRequest extends AbstractUserInfoAwareRequest
{
    protected const EXCEPTION = 'Validation failed: invalid code supplied.';

    #[Assert\NotNull]
    #[Assert\Type(type: 'string')]
    #[Assert\Type(type: 'digit')]
    protected $code;

    public function getCode(): string
    {
        return $this->code;
    }
}
