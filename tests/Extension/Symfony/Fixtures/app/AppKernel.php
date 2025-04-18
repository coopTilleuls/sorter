<?php

declare(strict_types=1);

use Sorter\Tests\Extension\Symfony\Fixtures\Controller\ArraySortController;
use Sorter\Tests\Extension\Symfony\Fixtures\Controller\DoublePrefixArraySortController;
use Sorter\Tests\Extension\Symfony\Fixtures\Controller\PrefixArraySortController;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class AppKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Sorter\Extension\Symfony\Bundle\SorterBundle(),
        ];
    }

    protected function configureContainer(ContainerBuilder $container): void
    {
        $container->setParameter('kernel.debug', true);
        $container->setParameter('kernel.secret', uniqid());

        $container->prependExtensionConfig('framework', ['test' => true, 'profiler' => true]);
        $container->prependExtensionConfig('twig', ['paths' => [__DIR__.'/templates']]);

        $container
            ->setDefinition(ArraySortController::class, new Definition(ArraySortController::class))
            ->setAutoconfigured(true)
            ->setAutowired(true)
            ->addTag('controller.service_arguments');

        $container
            ->setDefinition(PrefixArraySortController::class, new Definition(PrefixArraySortController::class))
            ->setAutoconfigured(true)
            ->setAutowired(true)
            ->addTag('controller.service_arguments');

        $container
            ->setDefinition(DoublePrefixArraySortController::class, new Definition(DoublePrefixArraySortController::class))
            ->setAutoconfigured(true)
            ->setAutowired(true)
            ->addTag('controller.service_arguments');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes
            ->add('array-sort', '/array-sort')
                ->controller(ArraySortController::class);

        $routes
            ->add('array-sort-prefix', '/array-sort-prefix')
                ->controller(PrefixArraySortController::class);
        $routes
            ->add('array-sort-double-prefix', '/array-sort-double-prefix')
                ->controller(DoublePrefixArraySortController::class);
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }
}
