<?php

declare(strict_types=1);

namespace App\Event\Verification;

use DateTimeInterface;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractVerificationEvent extends Event
{
    protected string $id;
    protected int $code;
    protected array $subject;
    protected DateTimeInterface $occurredOn;

    public function __construct(string $id, int $code, array $subject, DateTimeInterface $occurredOn)
    {
        $this->id = $id;
        $this->code = $code;
        $this->subject = $subject;
        $this->occurredOn = $occurredOn;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getSubject(): array
    {
        return $this->subject;
    }

    public function getOccurredOn(): DateTimeInterface
    {
        return $this->occurredOn;
    }
}
