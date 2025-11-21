<?php

declare(strict_types=1);

namespace Sorter\Tests\Applier;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sorter\Applier\ArrayApplier;
use Sorter\Comparable;
use Sorter\Exception\IncompatibleApplierException;
use Sorter\Sort;

final class ArrayApplierTest extends TestCase
{
    public function testSupportsArray(): void
    {
        $this->assertTrue((new ArrayApplier())->supports([]));
    }

    public function testNotSupportsThings(): void
    {
        $this->assertFalse((new ArrayApplier())->supports(new \stdClass()));
    }

    public function testItThrowsInIncompatibleData(): void
    {
        $this->expectException(IncompatibleApplierException::class);

        (new ArrayApplier())->apply($this->createMock(Sort::class), new \stdClass());
    }

    public function testItDoesNotCrashWithEmptySort(): void
    {
        /** @var Sort&MockObject $sort */
        $toBeSorted = [
            ['a' => 123],
            ['a' => 456],
            ['a' => 789],
        ];

        $sort = $this->createMock(Sort::class);
        $sort->method('getFields')->willReturn([]);

        $this->assertSame(
            [
                ['a' => 123],
                ['a' => 456],
                ['a' => 789],
            ],
            (new ArrayApplier())->apply($sort, $toBeSorted)
        );
    }

    public function testSortBasicArray(): void
    {
        /** @var Sort&MockObject $sort */
        $toBeSorted = [
            ['a' => 123],
            ['a' => 456],
            ['a' => 789],
        ];

        $sort = $this->createMock(Sort::class);
        $sort->method('getFields')->willReturn(['a']);
        $sort->method('getPath')->with('a')->willReturn('[a]');
        $sort->method('getDirection')->with('a')->willReturn('DESC');

        $this->assertSame(
            [
                ['a' => 789],
                ['a' => 456],
                ['a' => 123],
            ],
            (new ArrayApplier())->apply($sort, $toBeSorted)
        );
    }

    public function testSortBasicArrayWithoutExplicitPath(): void
    {
        /** @var Sort&MockObject $sort */
        $toBeSorted = [
            ['a' => 123],
            ['a' => 456],
            ['a' => 789],
        ];

        $sort = $this->createMock(Sort::class);
        $sort->method('getFields')->willReturn(['a']);
        $sort->method('getPath')->with('a')->willReturn('a');
        $sort->method('getDirection')->with('a')->willReturn('DESC');

        $this->assertSame(
            [
                ['a' => 789],
                ['a' => 456],
                ['a' => 123],
            ],
            (new ArrayApplier())->apply($sort, $toBeSorted)
        );
    }

    public function testItSortEnumArray(): void
    {
        $toBeSorted = [['enum' => SortableBackedEnum::B], ['enum' => SortableBackedEnum::A], ['enum' => SortableBackedEnum::C]];

        $sort = new Sort();
        $sort->add('enum', '[enum]', 'ASC');

        $this->assertSame(
            [['enum' => SortableBackedEnum::A], ['enum' => SortableBackedEnum::B], ['enum' => SortableBackedEnum::C]],
            (new ArrayApplier())->apply($sort, $toBeSorted),
        );

        $sort->add('enum', '[enum]', 'DESC');
        $this->assertSame(
            [['enum' => SortableBackedEnum::C], ['enum' => SortableBackedEnum::B], ['enum' => SortableBackedEnum::A]],
            (new ArrayApplier())->apply($sort, $toBeSorted),
        );
    }

    public function testItSortObjectArrayArray(): void
    {
        $a = new SortableObject('z', 'a');
        $b = new SortableObject('y', 'b');
        $c = new SortableObject('x', 'c');
        $toBeSorted = [['object' => $b], ['object' => $a], ['object' => $c]];

        $sort = new Sort();
        $sort->add('object', '[object]', 'ASC');

        $this->assertSame(
            [['object' => $a], ['object' => $b], ['object' => $c]],
            (new ArrayApplier())->apply($sort, $toBeSorted),
        );

        $sort->add('object', '[object]', 'DESC');
        $this->assertSame(
            [['object' => $c], ['object' => $b], ['object' => $a]],
            (new ArrayApplier())->apply($sort, $toBeSorted),
        );
    }

    public function testItSortObjectArray(): void
    {
        $a = new SortableObject('z', 'a');
        $b = new SortableObject('y', 'b');
        $c = new SortableObject('x', 'c');
        $toBeSorted = [$b, $a, $c];

        $sort = new Sort();
        $sort->add('value2', 'value2', 'ASC');

        $this->assertSame(
            [$a, $b, $c],
            (new ArrayApplier())->apply($sort, $toBeSorted),
        );

        $sort->add('value2', 'value2', 'DESC');
        $this->assertSame(
            [$c, $b, $a],
            (new ArrayApplier())->apply($sort, $toBeSorted),
        );
    }
}

enum SortableBackedEnum: string
{
    case A = 'a';
    case B = 'b';
    case C = 'c';
}

/**
 * @implements Comparable<SortableObject>
 */
final class SortableObject implements Comparable
{
    public function __construct(public readonly string $value1, public readonly string $value2)
    {
    }

    #[\Override]
    public function compare(Comparable $other): int
    {
        return $this->value2 <=> $other->value2;
    }
}
