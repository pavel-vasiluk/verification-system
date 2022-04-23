<?php

declare(strict_types=1);

namespace App\Event\Notification;

use DateTimeInterface;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractNotificationEvent extends Event implements JsonSerializable
{
    protected string $id;
    protected DateTimeInterface $occurredOn;

    public function __construct(string $id, DateTimeInterface $occurredOn)
    {
        $this->id = $id;
        $this->occurredOn = $occurredOn;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getOccurredOn(): DateTimeInterface
    {
        return $this->occurredOn;
    }

    #[ArrayShape(['id' => 'string', 'occurredOn' => DateTimeInterface::class])]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'occurredOn' => $this->getOccurredOn(),
        ];
    }
}
