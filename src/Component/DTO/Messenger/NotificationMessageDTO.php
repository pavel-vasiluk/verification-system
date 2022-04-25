<?php

declare(strict_types=1);

namespace App\Component\DTO\Messenger;

use App\Component\DTO\AbstractDTO;
use App\Enums\NotificationChannels;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;

class NotificationMessageDTO extends AbstractDTO implements JsonSerializable
{
    #[Assert\NotNull]
    #[Assert\Type(type: 'string')]
    protected $id;

    #[Assert\NotNull]
    #[Assert\Type(type: 'string')]
    protected $recipient;

    #[Assert\NotNull]
    #[Assert\Type(type: 'string')]
    #[Assert\Choice(choices: NotificationChannels::CHANNELS, strict: true)]
    protected $channel;

    #[Assert\NotNull]
    #[Assert\Type(type: 'string')]
    protected $body;

    public function getId(): string
    {
        return $this->id;
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    #[ArrayShape(['id' => "string", 'recipient' => "string", 'channel' => "string", 'body' => "string"])]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'recipient' => $this->getRecipient(),
            'channel' => $this->getChannel(),
            'body' => $this->getBody(),
        ];
    }
}
