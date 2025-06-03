<?php

use Sorter\Applier\SqlApplier;
use Sorter\Sort;
use Sorter\SorterFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$sql = 'SELECT p, COUNT(p.comments) FROM post p INNER JOIN p.comments comments GROUP BY p.id';

$factory = new SorterFactory([new SqlApplier()]);
$sorter = $factory->createSorter()
    ->add('title', 'p.title')
    ->add('date', 'p.date')
    ->add('weight', 'p.weight')
    ->add('comments', 'COUNT(p.comments)')
    ->addDefault('date', Sort::ASC);


echo "\n\n Default sort (Ascending date):\n";
$sorter->handle([]);
/** @var string $sortedSql */
$sortedSql = $sorter->sort($sql);
echo " ", $sortedSql, "\n";


echo "\n\n Single column sort (Ascending title):\n";
$sorter->handle(['title' => 'ASC']);
/** @var string $sortedSql */
$sortedSql = $sorter->sort($sql);
echo " ", $sortedSql, "\n";


echo "\n\n Double column sort (Ascending Weight, Ascending title):\n";
$sorter->handle(['weight' => 'ASC', 'title' => 'ASC']);
/** @var string $sortedSql */
$sortedSql = $sorter->sort($sql);
echo " ", $sortedSql, "\n";
