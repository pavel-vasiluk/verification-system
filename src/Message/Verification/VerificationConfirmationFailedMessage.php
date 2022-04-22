<?php

declare(strict_types=1);

namespace App\Message\Verification;

use DateTimeInterface;
use JetBrains\PhpStorm\ArrayShape;

class VerificationConfirmationFailedMessage extends AbstractVerificationMessage
{
    private string $reason;

    public function __construct(string $id, int $code, array $subject, DateTimeInterface $occurredOn, string $reason)
    {
        parent::__construct($id, $code, $subject, $occurredOn);
        $this->reason = $reason;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    #[ArrayShape([
        'id' => 'string',
        'code' => 'int',
        'subject' => 'array',
        'occurredOn' => DateTimeInterface::class,
        'reason' => 'string',
    ])]
    public function jsonSerialize(): array
    {
        return parent::jsonSerialize() + [
            'reason' => $this->getReason(),
        ];
    }
}
