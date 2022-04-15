<?php

declare(strict_types=1);

namespace App\Component\Request;

use Symfony\Component\HttpFoundation\Request;

abstract class AbstractRequest
{
    protected const EXCEPTION = 'Validation failed.';

    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getException(): string
    {
        return static::EXCEPTION;
    }
}
