<?php

declare(strict_types=1);

namespace App\Middleware\Verification\Confirmation\Handler;

use App\Component\Request\Verification\VerificationConfirmationRequest;
use App\Entity\Verification;
use App\Event\Verification\VerificationConfirmedEvent;
use App\Exception\InvalidVerificationCodeException;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class VerificationConfirmationHandler extends AbstractConfirmationHandler
{
    /**
     * @throws InvalidVerificationCodeException
     */
    public function process(VerificationConfirmationRequest $request, ?Verification $verification = null): void
    {
        $verification
            ?->setConfirmed(true)
            ?->setIsExpired(true)
        ;
        $this->entityManager->flush();

        $event = new VerificationConfirmedEvent(
            $verification?->getId()?->toString(),
            Response::HTTP_NO_CONTENT,
            $verification?->getSubject(),
            Carbon::now()
        );
        $this->eventDispatcher->dispatch($event);
    }
}
