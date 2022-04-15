<?php

declare(strict_types=1);

namespace App\Resolver;

use App\Component\Request\AbstractRequest;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestDtoValueResolver implements ArgumentValueResolverInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        try {
            $reflection = new ReflectionClass($argument->getType());

            return $reflection->isSubclassOf(AbstractRequest::class);
        } catch (ReflectionException) {
            return false;
        }
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $requestClass = $argument->getType();
        $requestDto = new $requestClass($request);

        $errors = $this->validator->validate($requestDto);

        if (count($errors) > 0) {
            // TODO: Throw validation exception that afterwards will be handled by event listener
            throw new Exception('Validation failed.');
        }

        yield $requestDto;
    }
}
