<?php

use Sorter\Applier\ArrayApplier;
use Sorter\Comparable;
use Sorter\SorterFactory;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * @template-implements Comparable<CustomObject>
 */
final class CustomObject implements Comparable
{
    public function __construct(
        public string $value1,
        public string $value2,
    ) {
    }

    #[\Override]
    public function compare(Comparable $other): int
    {
        return $this->value2 <=> $other->value2;
    }
}

/**
 * @param array{'object': CustomObject}[] $data
 */
function display_custom_object(array $data): void
{
    $horizontalLine = ' ---------------------' . PHP_EOL;

    echo $horizontalLine;
    echo ' | Value 1 | Value 2 |' . PHP_EOL;
    echo $horizontalLine;

    foreach ([...$data] as $row) {
        echo
            ' |  ' .
            $row['object']->value1 . '     ' .
            ' |  ' .
            $row['object']->value2 . '     ' .
            ' |' .
            PHP_EOL;
    }

    echo $horizontalLine;
}

$factory = new SorterFactory([new ArrayApplier()]);
$sorter = $factory->createSorter()
    ->add('object', '[object]')
    ->addDefault('object', 'ASC');


$data = [
    ['object' => new CustomObject('x', 'c')],
    ['object' => new CustomObject('v', 'e')],
    ['object' => new CustomObject('y', 'b')],
    ['object' => new CustomObject('z', 'a')],
    ['object' => new CustomObject('w', 'd')],
];

echo "\n";
display_custom_object($data);

$sorter->handle([]);
$data = $sorter->sort($data);

display_custom_object($data);
