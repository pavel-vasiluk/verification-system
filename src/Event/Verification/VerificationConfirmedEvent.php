<?php

declare(strict_types=1);

namespace App\Event\Verification;

class VerificationConfirmedEvent extends AbstractVerificationEvent
{
    public const NAME = 'verification.confirmed';
}
