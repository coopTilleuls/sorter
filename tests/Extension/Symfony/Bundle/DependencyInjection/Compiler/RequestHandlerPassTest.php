<?php

declare(strict_types=1);

namespace Sorter\Tests\Extension\Symfony\Bundle\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Sorter\Extension\Symfony\Bundle\DependencyInjection\Compiler\RequestHandlerPass;
use Sorter\Handler\RequestHandlerCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RequestHandlerPassTest extends TestCase
{
    public function testAppliersAreRegistered(): void
    {
        $containerBuilder = $this->createMock(ContainerBuilder::class);

        $containerBuilder->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with('sorter.request_handler')
            ->willReturn([
                'sorter.request_handler.test' => [[]],
            ]);

        $sorterFactoryDefinition = $this->createMock(Definition::class);

        $sorterFactoryDefinition
            ->expects($this->once())
            ->method('replaceArgument')
            ->with('$handlers', [new Reference('sorter.request_handler.test')]);

        $containerBuilder
            ->expects($this->once())
            ->method('getDefinition')
            ->with(RequestHandlerCollection::class)
            ->willReturn($sorterFactoryDefinition);

        (new RequestHandlerPass())->process($containerBuilder);
    }
}
