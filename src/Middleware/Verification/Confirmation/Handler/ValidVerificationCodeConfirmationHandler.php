<?php

declare(strict_types=1);

namespace App\Middleware\Verification\Confirmation\Handler;

use App\Component\Request\Verification\VerificationConfirmationRequest;
use App\Entity\Verification;
use App\Exception\InvalidVerificationCodeException;

class ValidVerificationCodeConfirmationHandler extends AbstractConfirmationHandler
{
    /**
     * @throws InvalidVerificationCodeException
     */
    public function process(VerificationConfirmationRequest $request, ?Verification $verification = null): void
    {
        if ($request->getCode() !== $verification?->getCode()) {
            $this->updateConfirmationAttempts($verification);

            // TODO: dispatch VerificationConfirmationFailed event

            throw new InvalidVerificationCodeException();
        }

        $this->processNext($verification, $request);
    }

    private function updateConfirmationAttempts(Verification $verification): void
    {
        $verification?->setConfirmationAttempts($verification->getConfirmationAttempts() + 1);

        if (Verification::MAX_CONFIRMATION_ATTEMPTS === $verification->getConfirmationAttempts()) {
            $verification->setIsExpired(true);
        }

        $this->entityManager->flush();
    }
}
