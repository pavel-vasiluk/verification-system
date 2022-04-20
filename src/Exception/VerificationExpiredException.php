<?php

declare(strict_types=1);

namespace App\Exception;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response;

class VerificationExpiredException extends AbstractRequestException
{
    protected $message = 'Verification expired.';

    #[Pure]
    public function __construct()
    {
        parent::__construct($this->message, Response::HTTP_GONE);
    }
}
