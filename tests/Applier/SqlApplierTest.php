<?php

declare(strict_types=1);

namespace Sorter\Tests\Applier;

use PHPUnit\Framework\TestCase;
use Sorter\Applier\SqlApplier;
use Sorter\Exception\IncompatibleApplierException;
use Sorter\Exception\IncompatibleQueryException;
use Sorter\Sort;

final class SqlApplierTest extends TestCase
{
    private readonly SqlApplier $applier;

    protected function setUp(): void
    {
        $this->applier = new SqlApplier();
    }

    public function testItSupportsSql(): void
    {
        $this->assertTrue($this->applier->supports('SELECT * FROM users'));
        $this->assertTrue($this->applier->supports('SELECT name, COUNT(id) FROM users GROUP BY name ORDER BY name'));
        $this->assertTrue($this->applier->supports('SELECT users.name, COUNT(users.id) FROM users INNER JOIN profile ON profile.user_id = users.id GROUP BY users.name ORDER BY ARRAY_POSITION(ARRAY[\'foo\', \'bar\', \'baz\'], users.name)'));
    }

    public function testItDoesNotSupportNonSql(): void
    {
        $this->assertFalse($this->applier->supports(new \stdClass()));
        $this->assertFalse($this->applier->supports([]));
        $this->assertFalse($this->applier->supports('This is not a SQL query'));
        $this->assertFalse($this->applier->supports('êêê'));
    }

    public function testItThrowsInIncompatibleData(): void
    {
        $this->expectException(IncompatibleApplierException::class);

        $this->applier->apply($this->createMock(Sort::class), []);
    }

    public function testItThrowsInIncompatibleQuery(): void
    {
        $this->expectException(IncompatibleQueryException::class);

        $this->applier->apply(
            $this->createMock(Sort::class),
            'WITH RECURSIVE (SELECT * FROM users)',
        );
    }

    public function testItDoesBasicSort(): void
    {
        $sql = 'SELECT * FROM users ORDER BY users.a ASC';
        $sort = $this->createMock(Sort::class);
        $sort->method('getFields')->willReturn(['a']);
        $sort->method('getPath')->with('a')->willReturn('users.a');
        $sort->method('getDirection')->with('a')->willReturn('DESC');

        $this->assertSame(
            'SELECT * FROM users ORDER BY users.a DESC',
            $this->applier->apply($sort, $sql),
        );
    }

    public function testItDoesBasicSortWithPreExistingSort(): void
    {
        $sql = 'SELECT * FROM users ORDER BY users.name ASC';
        $sort = $this->createMock(Sort::class);
        $sort->method('getFields')->willReturn(['a']);
        $sort->method('getPath')->with('a')->willReturn('users.a');
        $sort->method('getDirection')->with('a')->willReturn('DESC');

        $this->assertSame(
            'SELECT * FROM users ORDER BY users.name ASC, users.a DESC',
            $this->applier->apply($sort, $sql),
        );
    }

    public function testItDoesTwoColumnsSortWithPreExistingSort(): void
    {
        $sql = 'SELECT * FROM users ORDER BY users.name ASC';
        $sort = $this->createMock(Sort::class);
        $sort->method('getFields')->willReturn(['a', 'c']);
        $sort->method('getPath')->willReturnCallback(fn (string $f) => 'a' === $f ? 'users.a' : 'users.c');
        $sort->method('getDirection')->willReturnCallback(fn (string $f) => 'a' === $f ? 'DESC' : 'ASC');

        $this->assertSame(
            'SELECT * FROM users ORDER BY users.name ASC, users.a DESC, users.c ASC',
            $this->applier->apply($sort, $sql),
        );
    }

    public function testItSortWithComplexPostgresQuery(): void
    {
        $sql = 'SELECT users.name, COUNT(users.id) FROM users INNER JOIN profile ON profile.user_id = users.id GROUP BY users.name ORDER BY ARRAY_POSITION(ARRAY[\'foo\', \'bar\', \'baz\'], users.name)';
        $sort = $this->createMock(Sort::class);
        $sort->method('getFields')->willReturn(['name']);
        $sort->method('getPath')->with('name')->willReturn('ARRAY_POSITION(ARRAY[\'foo\', \'bar\', \'baz\'], users.name)');
        $sort->method('getDirection')->with('name')->willReturn('DESC');

        $this->assertSame(
            'SELECT users.name, COUNT(users.id) FROM users INNER JOIN profile ON profile.user_id = users.id GROUP BY users.name ORDER BY ARRAY_POSITION(ARRAY[\'foo\', \'bar\', \'baz\'], users.name) DESC',
            $this->applier->apply($sort, $sql),
        );
    }
}
