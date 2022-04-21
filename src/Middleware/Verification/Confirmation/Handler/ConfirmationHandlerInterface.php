<?php

declare(strict_types=1);

namespace App\Middleware\Verification\Confirmation\Handler;

use App\Component\Request\Verification\VerificationConfirmationRequest;
use App\Entity\Verification;

interface ConfirmationHandlerInterface
{
    public function process(VerificationConfirmationRequest $request, ?Verification $verification = null): void;

    public function setSuccessor(?ConfirmationHandlerInterface $successor): void;
}
