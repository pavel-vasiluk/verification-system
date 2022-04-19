<?php

declare(strict_types=1);

namespace App\Component\DTO;

abstract class AbstractDTO
{
    public function __construct(array $objectData)
    {
        foreach ($this as $parameterName => $parameterValue) {
            $this->{$parameterName} = $objectData[$parameterName] ?? null;
        }
    }
}
