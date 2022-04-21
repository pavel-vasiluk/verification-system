<?php

declare(strict_types=1);

namespace App\Middleware\Verification\Confirmation\Handler;

use App\Component\Request\Verification\VerificationConfirmationRequest;
use App\Entity\Verification;
use App\Event\Verification\VerificationConfirmationFailedEvent;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractConfirmationHandler implements ConfirmationHandlerInterface
{
    protected ?ConfirmationHandlerInterface $successor = null;
    protected EntityManagerInterface $entityManager;
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    final public function setSuccessor(?ConfirmationHandlerInterface $successor): void
    {
        $this->successor = $successor;
    }

    final public function processNext(Verification $verification, VerificationConfirmationRequest $request): void
    {
        if (!$this->successor) {
            return;
        }

        $this->successor->process($request, $verification);
    }

    final public function dispatchConfirmationFailedEvent(string $id, int $code, array $subject = []): void
    {
        $event = new VerificationConfirmationFailedEvent($id, $code, $subject, Carbon::now());

        $this->eventDispatcher->dispatch($event);
    }
}
