<?php

declare(strict_types=1);

namespace App\Resolver;

use App\Component\Request\AbstractRequest;
use App\Exception\RequestValidationException;
use ReflectionClass;
use ReflectionException;
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

    /**
     * @throws RequestValidationException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $requestClass = $argument->getType();

        /** @var AbstractRequest $requestDto */
        $requestDto = new $requestClass($request);

        $errors = $this->validator->validate($requestDto);

        if (count($errors) > 0) {
            throw new RequestValidationException($requestDto->getException());
        }

        yield $requestDto;
    }
}
