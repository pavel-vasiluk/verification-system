<?php

declare(strict_types=1);

namespace App\Exception;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response;

class InvalidVerificationCodeException extends AbstractRequestException
{
    protected $message = 'Validation failed: invalid code supplied.';

    #[Pure]
    public function __construct()
    {
        parent::__construct($this->message, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
