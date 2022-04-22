<?php

declare(strict_types=1);

namespace App\Helper;

class VerificationCodeGenerationHelper
{
    public static function generateVerificationCode(int $digits): string
    {
        return (string) random_int(10 ** ($digits - 1), (10 ** $digits) - 1);
    }
}
