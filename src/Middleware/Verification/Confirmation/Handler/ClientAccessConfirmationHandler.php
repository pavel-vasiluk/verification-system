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
    public function process(VerificationConfirmationRequest $request, ?Verification $verification = null): void
    {
        if ($verification?->getUserInfo() !== $request->getUserInfo()->jsonSerialize()) {
            $exception = new VerificationAccessDeniedException();
            $this->dispatchConfirmationFailedEvent(
                $verification?->getId()?->toString(),
                $exception->getCode(),
                $exception->getMessage()
            );

            throw $exception;
        }

        $this->processNext($verification, $request);
    }
}
