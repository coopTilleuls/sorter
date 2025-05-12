<?php

declare(strict_types=1);

use Sorter\Applier\ArrayApplier;
use Sorter\Applier\DoctrineORMApplier;
use Sorter\Applier\SqlApplier;
use Sorter\Builder\QueryParamUrlBuilder;
use Sorter\Builder\UrlBuilder;
use Sorter\Extension\Twig\SortExtension;
use Sorter\SorterFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services
            ->defaults()
                ->autowire()
                ->autoconfigure();

    $services
        ->set(ArrayApplier::class)
            ->tag('sorter.applier');

    $services
        ->set(DoctrineORMApplier::class)
            ->tag('sorter.applier');

    $services
        ->set(SqlApplier::class)
            ->tag('sorter.applier');

    $services
        ->set(UrlBuilder::class, QueryParamUrlBuilder::class)
            ->public();

    $services
        ->set(SorterFactory::class)
            ->public()
            ->args(['$appliers' => []]);

    $services
        ->alias('sorter.factory', SorterFactory::class)
            ->public();

    $services
        ->set(SortExtension::class);
};
