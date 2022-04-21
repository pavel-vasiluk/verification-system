<?php

declare(strict_types=1);

namespace App\Middleware\Verification\Confirmation\Handler;

use App\Component\Request\Verification\VerificationConfirmationRequest;
use App\Entity\Verification;
use App\Exception\VerificationNotFoundException;

class VerificationExistenceConfirmationHandler extends AbstractConfirmationHandler
{
    /**
     * @throws VerificationNotFoundException
     */
    public function process(?Verification $verification, VerificationConfirmationRequest $request): void
    {
        if (!$verification) {
            // TODO: dispatch VerificationConfirmationFailed event

            throw new VerificationNotFoundException();
        }

        $this->processNext($verification, $request);
    }
}
