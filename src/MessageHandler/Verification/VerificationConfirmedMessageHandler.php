<?php

declare(strict_types=1);

namespace App\MessageHandler\Verification;

use App\Message\Verification\VerificationConfirmedMessage;
use JsonException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class VerificationConfirmedMessageHandler
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $verificationLogger)
    {
        $this->logger = $verificationLogger;
    }

    /**
     * @throws JsonException
     */
    public function __invoke(VerificationConfirmedMessage $message): void
    {
        $this->logger->info(
            sprintf(
                'Verification %s has been successfully confirmed. Message payload: %s',
                $message->getId(),
                json_encode($message, JSON_THROW_ON_ERROR)
            )
        );
    }
}
