<?php

declare(strict_types=1);

namespace App\Middleware\Verification\Confirmation\Handler;

use App\Component\Request\Verification\VerificationConfirmationRequest;
use App\Entity\Verification;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractConfirmationHandler implements ConfirmationHandlerInterface
{
    protected ?ConfirmationHandlerInterface $successor = null;
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    final public function setSuccessor(?ConfirmationHandlerInterface $successor): void
    {
        $this->successor = $successor;
    }

    final public function processNext(?Verification $verification, VerificationConfirmationRequest $request): void
    {
        if (!$this->successor) {
            return;
        }

        $this->successor->process($verification, $request);
    }
}
