<?php

declare(strict_types=1);

namespace App\Component\Request;

use App\Exception\MalformedJsonException;
use JsonException;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractJsonBodyRequest extends AbstractRequest
{
    /**
     * @throws MalformedJsonException
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $content = $this->request->getContent();

        if (empty($content)) {
            return;
        }

        try {
            $parameters = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new MalformedJsonException();
        }

        foreach ($this as $parameterName => $parameterValue) {
            if ($parameterValue instanceof Request) {
                continue;
            }

            $this->{$parameterName} = $parameters[$parameterName] ?? null;
        }
    }
}
