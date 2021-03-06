<?php

declare(strict_types=1);

namespace App\Enums;

abstract class NotificationChannels
{
    public const SMS_CHANNEL = 'sms';
    public const EMAIL_CHANNEL = 'email';

    public const CHANNELS = [
        self::SMS_CHANNEL,
        self::EMAIL_CHANNEL,
    ];
}
