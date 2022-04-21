<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfirmationHandlerProviderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $locationHandlers = $container->findTaggedServiceIds('verification_handler');

        $handlersByPriority = [];
        foreach ($locationHandlers as $id => $attributes) {
            $priority = (int) ($attributes[0]['priority'] ?? 0);

            $handlersByPriority[$priority][] = $id;
        }

        $sortedHandlers = $this->sortHandlers($handlersByPriority);

        $firstHandlerServiceName = array_shift($sortedHandlers);

        $firstHandler = new Alias($firstHandlerServiceName);
        $firstHandler->setPublic(false);
        $container->setAlias('app.verification.verification_handler', $firstHandler);
        $handlerDefinition = $container->getDefinition($firstHandlerServiceName);

        foreach ($sortedHandlers as $handler) {
            $handler = $container->getDefinition($handler);
            $handlerDefinition->addMethodCall('setSuccessor', [$handler]);
            $handlerDefinition = $handler;
        }
    }

    private function sortHandlers(array $providersByPriority): array
    {
        krsort($providersByPriority);

        return array_merge(...$providersByPriority);
    }
}
