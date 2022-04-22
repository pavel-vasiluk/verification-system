<?php

declare(strict_types=1);

namespace App\Event\Verification;

use DateTimeInterface;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractVerificationEvent extends Event implements JsonSerializable
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

    #[ArrayShape(['id' => 'string', 'code' => 'int', 'subject' => 'array', 'occurredOn' => DateTimeInterface::class])]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'code' => $this->getCode(),
            'subject' => $this->getSubject(),
            'occurredOn' => $this->getOccurredOn(),
        ];
    }
}
