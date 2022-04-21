<?php

declare(strict_types=1);

namespace App\Middleware\Verification\Confirmation\Handler;

use App\Component\Request\Verification\VerificationConfirmationRequest;
use App\Entity\Verification;
use App\Exception\VerificationNotFoundException;
use App\Repository\VerificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class VerificationExistenceConfirmationHandler extends AbstractConfirmationHandler
{
    private VerificationRepository $verificationRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        VerificationRepository $verificationRepository
    ) {
        parent::__construct($entityManager, $eventDispatcher);
        $this->verificationRepository = $verificationRepository;
    }

    /**
     * @throws VerificationNotFoundException
     */
    public function process(VerificationConfirmationRequest $request, ?Verification $verification = null): void
    {
        if (!$verification) {
            $verificationUuid = $request->getRequest()->attributes->get('id', '');

            if (!$verification = $this->verificationRepository->find($verificationUuid)) {
                $exception = new VerificationNotFoundException();
                $this->dispatchConfirmationFailedEvent($verificationUuid, $exception->getCode());

                throw $exception;
            }
        }

        $this->processNext($verification, $request);
    }
}
