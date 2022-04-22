<?php

declare(strict_types=1);

namespace App\MessageHandler\Verification;

use App\Message\Verification\EmailVerificationCreatedMessage;
use JsonException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class EmailVerificationCreatedMessageHandler extends AbstractVerificationCreatedMessageHandler
{
    /**
     * @throws JsonException
     */
    public function __invoke(EmailVerificationCreatedMessage $message): void
    {
        $this->logVerificationCreatedMessage($message);
    }
}
