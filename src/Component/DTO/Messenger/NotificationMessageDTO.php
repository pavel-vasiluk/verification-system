<?php

declare(strict_types=1);

namespace App\Component\DTO\Messenger;

use App\Component\DTO\AbstractDTO;
use App\Enums\NotificationChannels;
use Symfony\Component\Validator\Constraints as Assert;

class NotificationMessageDTO extends AbstractDTO
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
}
