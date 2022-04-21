<?php

declare(strict_types=1);

namespace App\Event\Verification;

class VerificationConfirmationFailedEvent extends AbstractVerificationEvent
{
    public const NAME = 'verification.confirmation.failed';
}
