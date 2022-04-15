<?php

declare(strict_types=1);

namespace App\Exception;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response;

class MalformedJsonException extends AbstractRequestException
{
    protected $message = 'Malformed JSON passed.';

    #[Pure]
    public function __construct()
    {
        parent::__construct($this->message, Response::HTTP_BAD_REQUEST);
    }
}
