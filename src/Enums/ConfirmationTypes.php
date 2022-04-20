<?php

declare(strict_types=1);

namespace App\Enums;

abstract class ConfirmationTypes
{
    public const MOBILE_CONFORMATION = 'mobile_confirmation';
    public const EMAIL_CONFIRMATION = 'email_confirmation';

    public const CONFIRMATIONS = [
        self::MOBILE_CONFORMATION,
        self::EMAIL_CONFIRMATION,
    ];
}
