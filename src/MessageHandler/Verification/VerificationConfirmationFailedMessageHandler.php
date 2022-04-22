<?php

declare(strict_types=1);

namespace App\MessageHandler\Verification;

use App\Message\Verification\VerificationConfirmationFailedMessage;
use JsonException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class VerificationConfirmationFailedMessageHandler
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $verificationLogger)
    {
        $this->logger = $verificationLogger;
    }

    /**
     * @throws JsonException
     */
    public function __invoke(VerificationConfirmationFailedMessage $message): void
    {
        $this->logger->info(
            sprintf(
                'Verification confirmation failure report. Message payload: %s',
                json_encode($message, JSON_THROW_ON_ERROR)
            )
        );
    }
}
