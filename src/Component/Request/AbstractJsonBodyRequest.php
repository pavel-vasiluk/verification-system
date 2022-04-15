<?php

declare(strict_types=1);

namespace App\Component\Request;

use JsonException;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractJsonBodyRequest extends AbstractRequest
{
    /**
     * @throws JsonException
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $content = $this->request->getContent();

        if (empty($content)) {
            return;
        }

        $parameters = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        foreach ($this as $parameterName => $parameterValue) {
            if ($parameterValue instanceof Request) {
                continue;
            }

            $this->{$parameterName} = $parameters[$parameterName] ?? null;
        }
    }
}
