<?php

declare(strict_types=1);

namespace App\Event\Verification;

class VerificationCreatedEvent extends AbstractVerificationEvent
{
    public const NAME = 'verification.created';
}
