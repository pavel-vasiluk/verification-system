<?php

declare(strict_types=1);

namespace App\Middleware\Verification\Confirmation\Handler;

use App\Component\Request\Verification\VerificationConfirmationRequest;
use App\Entity\Verification;
use App\Exception\VerificationExpiredException;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ActiveStatusConfirmationHandler extends AbstractConfirmationHandler
{
    private int $verificationLifetime;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        int $verificationLifetime
    ) {
        parent::__construct($entityManager, $eventDispatcher);
        $this->verificationLifetime = $verificationLifetime;
    }

    /**
     * @throws VerificationExpiredException
     */
    public function process(VerificationConfirmationRequest $request, ?Verification $verification = null): void
    {
        if (!$verification?->isExpired() && $this->isVerificationExpiredByTime($verification)) {
            $verification?->setIsExpired(true);
            $this->entityManager->flush();
        }

        if ($verification?->isExpired()) {
            // TODO: dispatch VerificationConfirmationFailed event

            throw new VerificationExpiredException();
        }

        $this->processNext($verification, $request);
    }

    private function isVerificationExpiredByTime(Verification $verification): bool
    {
        $now = Carbon::now();

        return $now->diffInMinutes($verification->getCreatedAt()) >= $this->verificationLifetime;
    }
}
