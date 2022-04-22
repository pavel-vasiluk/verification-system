<?php

declare(strict_types=1);

namespace App\MessageHandler\Verification;

use App\Message\Verification\AbstractVerificationMessage;
use JsonException;
use Psr\Log\LoggerInterface;

abstract class AbstractVerificationCreatedMessageHandler
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $verificationLogger)
    {
        $this->logger = $verificationLogger;
    }

    /**
     * @throws JsonException
     */
    protected function logVerificationCreatedMessage(AbstractVerificationMessage $message): void
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
