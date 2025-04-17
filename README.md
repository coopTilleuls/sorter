Sorter
======

Sorter is a PHP column sorting library that allows you to apply sorts of any kind of data source.

[![tests](https://github.com/coopTilleuls/sorter/actions/workflows/ci.yml/badge.svg)](https://github.com/coopTilleuls/sorter/actions/workflows/ci.yml)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FcoopTilleuls%2Fsorter%2Fmain)](https://dashboard.stryker-mutator.io/reports/github.com/coopTilleuls/sorter/main)
[![Coverage Status](https://coveralls.io/repos/github/coopTilleuls/sorter/badge.svg?branch=main)](https://coveralls.io/github/coopTilleuls/sorter?branch=main)

Features
--------

 * Sorts any kind of data source
 * Sorts by multiple columns
 * Factorise sorting logic into definitions classes
 * Process HTTP request
 * Symfony Bundle
 * Twig extension

Installation
------------

```bash
 $ composer require tilleuls/sorter
```

### Optionnal : enable symfony bundle

```php title=config/bundles.php
<?php

return [
    // ...
    Sorter\Extension\Symfony\Bundle\SorterBundle::class => ['all' => true],
];

```

Usage
-----

Sorter provides a `SorterFactory` class that allows you to sort your data source. 

The factory require an applier to apply the sort to the data source.

### Basic sorting

```php

// Create the sorter factory (useless with Symfony)
$factory = new SorterFactory([new DoctrineORMApplier()]);

// Create your sorter definition
$sorter = $factory->createSorter()
    ->add('title', 'p.title')
    ->add('date', 'p.date')
    ->addDefault('date', Sort::ASC);

// Handle takes an array of data and transform it to a Sort object
$sorter->handle([]);

// Apply the sort to the data
$data = $sorter->sort($data);

```

### Symfony usage

With Symfony, the `SorterFactory` is available as a service.

```php
class IndexController
{
    public function __construct(
        private SorterFactory $factory,
        private PostRepository $repository,
        private Environment $twig,
    ) {
    }
    
    public function index(Request $request)
    {
        $sorter = $this->factory->createSorter()
            ->add('title', 'p.title')
            ->add('date', 'p.date')
            ->addDefault('date', Sort::ASC);
    
        $sorter->handleRequest($request);
        $qb = $sorter->sort($this->repository->createQueryBuilder('p'));
    
        return new Response(
            $this->twig->render(
                'array-sort.html.twig',
                [
                    'sorter' => $sorter,
                    'data' => $qb->getQuery()->getResult(),
                ],
            ),
        );
    }
}

```

### Definition class

You can factorise your sorting logic into a definition class.

```php

use Sorter\Definition;
use Sorter\Sorter;

class PostSortDefinition implements Definition
{
    public function buildSorter(Sorter $sorter): void
    {
        $sorter
            ->add('title', 'p.title')
            ->add('date', 'p.date')
            ->addDefault('date', Sort::ASC);
    }
}

```

```php
class IndexController
{
    public function __construct(
        private SorterFactory $factory,
        private PostRepository $repository,
        private Environment $twig,
    ) {
    }
    
    public function index(Request $request)
    {
        $sorter = $this->factory->createSorter(new PostSortDefinition());
        $sorter->handleRequest($request);
        $qb = $sorter->sort($this->repository->createQueryBuilder('p'));
    
        return new Response(
            $this->twig->render(
                'array-sort.html.twig',
                [
                    'sorter' => $sorter,
                    'data' => $qb->getQuery()->getResult(),
                ],
            ),
        );
    }
}

```
