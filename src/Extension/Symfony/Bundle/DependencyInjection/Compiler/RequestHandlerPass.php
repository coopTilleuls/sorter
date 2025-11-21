<?php

namespace Sorter\Extension\Symfony\Bundle\DependencyInjection\Compiler;

use Sorter\Handler\RequestHandlerCollection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RequestHandlerPass implements CompilerPassInterface
{
    #[\Override]
    public function process(ContainerBuilder $container): void
    {
        $handlers = [];
        foreach ($container->findTaggedServiceIds('sorter.request_handler') as $serviceId => $_) {
            $handlers[] = new Reference($serviceId);
        }

        $container->getDefinition(RequestHandlerCollection::class)->replaceArgument('$handlers', $handlers);
    }
}
