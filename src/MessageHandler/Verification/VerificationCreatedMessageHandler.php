<?php

declare(strict_types=1);

namespace App\MessageHandler\Verification;

use App\Message\Verification\VerificationCreatedMessage;
use JsonException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class VerificationCreatedMessageHandler
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $verificationLogger)
    {
        $this->logger = $verificationLogger;
    }

    /**
     * @throws JsonException
     */
    public function __invoke(VerificationCreatedMessage $message): void
    {
        $this->logger->info(
            sprintf(
                'Verification %s has been created. Message payload: %s',
                $message->getId(),
                json_encode($message, JSON_THROW_ON_ERROR)
            )
        );
    }
}
