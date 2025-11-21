<?php

declare(strict_types=1);

namespace Sorter\Tests\Extension\Symfony\Bundle;

use Sorter\Builder\QueryParamUrlBuilder;
use Sorter\Builder\UrlBuilder;
use Sorter\Extension\Symfony\Bundle\DependencyInjection\Compiler\ApplierCompilerPass;
use Sorter\Extension\Symfony\Bundle\SorterBundle;
use Sorter\SorterFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SorterBundleTest extends KernelTestCase
{
    public function testServiceAreRegistered(): void
    {
        $kernel = self::bootKernel();

        $this->assertTrue($kernel->getContainer()->has(UrlBuilder::class));
        $this->assertInstanceOf(QueryParamUrlBuilder::class, $kernel->getContainer()->get(UrlBuilder::class));
        $this->assertTrue($kernel->getContainer()->has(SorterFactory::class));
        $this->assertTrue($kernel->getContainer()->has('sorter.factory'));
    }

    public function testCompilerPassIsAdded(): void
    {
        $containerMock = $this->createMock(ContainerBuilder::class);
        $containerMock->expects($this->once())
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(ApplierCompilerPass::class));

        $bundle = new SorterBundle();
        $bundle->build($containerMock);
    }
}
