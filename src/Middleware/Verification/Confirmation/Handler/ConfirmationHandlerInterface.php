<?php

declare(strict_types=1);

namespace App\Middleware\Verification\Confirmation\Handler;

use App\Component\Request\Verification\VerificationConfirmationRequest;
use App\Entity\Verification;

interface ConfirmationHandlerInterface
{
    public function process(?Verification $verification, VerificationConfirmationRequest $request): void;

    public function setSuccessor(?ConfirmationHandlerInterface $successor): void;
}
