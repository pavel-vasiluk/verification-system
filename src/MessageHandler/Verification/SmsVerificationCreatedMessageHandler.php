<?php

declare(strict_types=1);

namespace App\MessageHandler\Verification;

use App\Message\Verification\SmsVerificationCreatedMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SmsVerificationCreatedMessageHandler extends AbstractVerificationCreatedMessageHandler
{
    public function __invoke(SmsVerificationCreatedMessage $message): void
    {
        $this->logVerificationCreatedMessage($message);
    }
}
