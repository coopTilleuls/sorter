<?php

declare(strict_types=1);

use Sorter\Applier\ArrayApplier;
use Sorter\Applier\DoctrineORMApplier;
use Sorter\Applier\SqlApplier;
use Sorter\Builder\QueryParamUrlBuilder;
use Sorter\Builder\UrlBuilder;
use Sorter\Extension\Twig\SortExtension;
use Sorter\Handler\RequestHandler;
use Sorter\Handler\RequestHandlerCollection;
use Sorter\Handler\SymfonyHttpFoundationRequestHandler;
use Sorter\SorterFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services
            ->defaults()
                ->autowire()
                ->autoconfigure();

    $services
        ->set(RequestHandlerCollection::class)
            ->args(['$handlers' => []]);

    $services
        ->alias(RequestHandler::class, RequestHandlerCollection::class);

    $services
        ->set(SymfonyHttpFoundationRequestHandler::class)
            ->tag('sorter.request_handler');

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
            ->args(
                [
                    '$appliers' => [],
                    '$requestHandler' => service(RequestHandler::class),
                ],
            );

    $services
        ->alias('sorter.factory', SorterFactory::class)
            ->public();

    $services
        ->set(SortExtension::class);
};
