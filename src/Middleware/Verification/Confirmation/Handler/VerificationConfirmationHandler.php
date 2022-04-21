<?php

declare(strict_types=1);

namespace App\Middleware\Verification\Confirmation\Handler;

use App\Component\Request\Verification\VerificationConfirmationRequest;
use App\Entity\Verification;
use App\Exception\InvalidVerificationCodeException;

class VerificationConfirmationHandler extends AbstractConfirmationHandler
{
    /**
     * @throws InvalidVerificationCodeException
     */
    public function process(?Verification $verification, VerificationConfirmationRequest $request): void
    {
        $verification
            ?->setConfirmed(true)
            ?->setIsExpired(true)
        ;
        $this->entityManager->flush();

        // TODO: dispatch VerificationConfirmed event
    }
}
