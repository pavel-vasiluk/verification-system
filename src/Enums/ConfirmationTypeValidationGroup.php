<?php

declare(strict_types=1);

namespace App\Enums;

abstract class ConfirmationTypeValidationGroup
{
    public const EMAIL_VALIDATION_GROUP = 'EmailValidation';
    public const MOBILE_VALIDATION_GROUP = 'MobileValidation';

    public const CONFIRMATION_TYPES_VALIDATION_GROUPS = [
        ConfirmationTypes::EMAIL_CONFIRMATION => self::EMAIL_VALIDATION_GROUP,
        ConfirmationTypes::MOBILE_CONFORMATION => self::MOBILE_VALIDATION_GROUP,
    ];
}
