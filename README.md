Sorter
======

Sorter is a PHP column sorting library that allows you to apply sorts of any kind of data source.

[![tests](https://github.com/coopTilleuls/sorter/actions/workflows/ci.yml/badge.svg)](https://github.com/coopTilleuls/sorter/actions/workflows/ci.yml)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FcoopTilleuls%2Fsorter%2Fmain)](https://dashboard.stryker-mutator.io/reports/github.com/coopTilleuls/sorter/main)
[![Coverage Status](https://coveralls.io/repos/github/coopTilleuls/sorter/badge.svg?branch=main)](https://coveralls.io/github/coopTilleuls/sorter?branch=main)

Features
--------

 * Sorts any kind of data source (array, Doctrine ORM, and plain SQL built-in)
 * Sorts by multiple columns
 * Factorize sorting logic into definitions classes
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

The factory builds a sorter instance and requires an applier to apply the sort to the data source.

The classic way to use it is the following :

 1. Create a `SorterFactory` instance (if you are not using Symfony)
 2. Create a sorter instance using the factory
 3. Define the sorting columns, the default sort, and eventually the query prefix


### Basic sorting

```php

// Create the sorter factory (useless with Symfony)
$factory = new SorterFactory([new DoctrineORMApplier()]);

// Create your sorter instance and make your definition
$sorter = $factory->createSorter()
    ->add('title', 'p.title')
    ->add('date', 'p.date')
    ->addDefault('date', Sort::ASC);

// Handle takes an array of data and transform it to a Sort object
$sorter->handle([]);

// Apply the sort to the data
$data = $sorter->sort($data);

```

The `Sorter\Sorter` class providers the following methods to define your sorts :
 * `$sorter->add(string $field, string $path)` : Adds a new column to the sorter. `$field` is the name of the column, 
    and `$path` is the path to the column in your data source. The path can be a SQL expression, a property name, or even an array key index.
 * `$sorter->addDefault(string $field, string $direction)` / `$sorter->removeDefault(string $field)` : Adds a default sort to the sorter.
   `$field` is the name of the column, and `$direction` is the direction of the sort (`Sort::ASC` or `Sort::DESC`).
 * `$sorter->setPrefix(string $prefix)` : Sets the prefix to be used in the query string. This is useful if you want to use several sorters in the same page.

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

You can factorize your sorting logic into a definition class. 
Definition classes are useful if you want to reuse the same sorting logic in several places.

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

### Twig extension

You can use the `SorterExtension` to render the sorting links in your Twig templates.

The twig extension provides the following functions:

 * `sorter_link` : Renders a ready to use and request aware sorting link. 
 * `sorter_url` : Renders a URL for the sorting link. This is useful if you want to use your own HTML.
 * `sorter_direction` : Returns the direction of the sort for a given column. This is useful if you want to use your own HTML.

#### Example

```twig
<table>
  <thead>
    <tr>
      <th scope="col" data-col="title">
        {{ sorter_link(sorter, 'title', 'Title') }}
      </th>
      <th scope="col" data-col="a">
        {{ sorter_link(sorter, 'a', 'A') }}
      </th>
      <th scope="col" data-col="b">
        <a href="{{ sorter_url(sorter, 'b') }}" class="SortLink SortLink--{{ sorter_direction(sorter, 'b') }}">B</a>
      </th>
      <th scope="col" data-col="c">
        <a href="{{ sorter_url(sorter, 'c') }}" class="SortLink SortLink--{{ sorter_direction(sorter, 'c') }}">
          C
        </a>
      </th>
    </tr>
  </thead>
  <tbody>
    {% for row in data %}
      <tr>
        <th scope="row">{{ row.title }}</th>
        <td>{{ row.a }}</td>
        <td>{{ row.b }}</td>
        <td>{{ row.c }}</td>
      </tr>
    {% endfor %}
  </tbody>
</table>

```
