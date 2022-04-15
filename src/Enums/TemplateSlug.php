<?php

declare(strict_types=1);

namespace App\Enums;

abstract class TemplateSlug
{
    public const MOBILE_VERIFICATION = 'mobile-verification';
    public const EMAIL_VERIFICATION = 'email-verification';

    public const VERIFICATIONS = [
        self::MOBILE_VERIFICATION,
        self::EMAIL_VERIFICATION,
    ];
}
