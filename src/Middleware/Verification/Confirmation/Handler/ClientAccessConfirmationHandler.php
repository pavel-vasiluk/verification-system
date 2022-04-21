<?php

declare(strict_types=1);

namespace App\Middleware\Verification\Confirmation\Handler;

use App\Component\Request\Verification\VerificationConfirmationRequest;
use App\Entity\Verification;
use App\Exception\VerificationAccessDeniedException;

class ClientAccessConfirmationHandler extends AbstractConfirmationHandler
{
    /**
     * @throws VerificationAccessDeniedException
     */
    public function process(?Verification $verification, VerificationConfirmationRequest $request): void
    {
        if ($verification?->getUserInfo() !== $request->getUserInfo()->jsonSerialize()) {
            // TODO: dispatch VerificationConfirmationFailed event

            throw new VerificationAccessDeniedException();
        }

        $this->processNext($verification, $request);
    }
}
