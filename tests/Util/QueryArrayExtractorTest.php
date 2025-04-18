<?php

namespace Sorter\Tests\Util;

use PHPUnit\Framework\Attributes\DataProvider;
use Sorter\Exception\CannotExtractException;
use Sorter\Util\QueryArrayExtractor;
use PHPUnit\Framework\TestCase;

final class QueryArrayExtractorTest extends TestCase
{
    #[DataProvider('extractDataProvider')]
    public function testExtract(?array $queryArray, ?string $prefix, array $expected): void
    {
        $this->assertSame($expected, QueryArrayExtractor::extract($queryArray, $prefix));
    }

    public static function extractDataProvider(): iterable
    {
        yield 'flat query' => [
            'queryArray' => ['a' => 'foo', 'b' => 'bar'],
            'prefix' => null,
            'expected' => ['a' => 'foo', 'b' => 'bar'],
        ];

        yield 'flat query, empty value' => [
            'queryArray' => null,
            'prefix' => null,
            'expected' => [],
        ];

        yield 'prefixed query' => [
            'queryArray' => ['prefix' => ['a' => 'foo', 'b' => 'bar']],
            'prefix' => 'prefix',
            'expected' => ['a' => 'foo', 'b' => 'bar'],
        ];

        yield 'prefixed query, empty value' => [
            'queryArray' => null,
            'prefix' => 'prefix',
            'expected' => [],
        ];

        yield 'double prefixed query' => [
            'queryArray' => ['prefix' => ['second' => ['a' => 'foo', 'b' => 'bar']]],
            'prefix' => 'prefix[second]',
            'expected' => ['a' => 'foo', 'b' => 'bar'],
        ];

        yield 'double prefixed query, empty value' => [
            'queryArray' => ['prefix' => null],
            'prefix' => 'prefix[second]',
            'expected' => [],
        ];

        yield 'triple prefixed query' => [
            'queryArray' => ['prefix' => ['second' => [ 'third' => ['a' => 'foo', 'b' => 'bar']]]],
            'prefix' => 'prefix[second][third]',
            'expected' => ['a' => 'foo', 'b' => 'bar'],
        ];
    }

    #[DataProvider('extractThrowsDataProvider')]
    public function testItThrowsWhenExtractParamsInvalid(array $queryArray, ?string $prefix, string $path): void
    {
        $this->expectException(CannotExtractException::class);
        $this->expectExceptionMessage('Invalid prefix format: []');

        QueryArrayExtractor::extract($queryArray, $prefix);
    }

    public static function extractThrowsDataProvider(): iterable
    {
        yield 'InvalidPrefix' => [
            'queryArray' => [],
            'prefix' => '[]',
            'path' => '',
        ];
    }
}
