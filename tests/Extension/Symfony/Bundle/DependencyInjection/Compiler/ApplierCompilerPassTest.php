<?php

declare(strict_types=1);

namespace Sorter\Tests\Extension\Symfony\Bundle\DependencyInjection\Compiler;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Sorter\Applier\SortApplier;
use Sorter\SorterFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ApplierCompilerPassTest extends KernelTestCase
{
    public function testAppliersAreRegistered(): void
    {
        $container = self::getContainer();
        $factory = $container->get(SorterFactory::class);

        $this->assertInstanceOf(SortApplier::class, $factory->getApplier([]));
        $this->assertInstanceOf(
            SortApplier::class,
            $factory->getApplier(new QueryBuilder($this->createMock(EntityManagerInterface::class))),
        );
    }
}
