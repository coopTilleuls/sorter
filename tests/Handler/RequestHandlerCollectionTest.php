<?php

declare(strict_types=1);

namespace Sorter\Tests\Handler;

use PHPUnit\Framework\TestCase;
use Sorter\Exception\NoHandlerException;
use Sorter\Handler\RequestHandlerCollection;
use Sorter\Handler\RequestHandlerStrategy;

final class RequestHandlerCollectionTest extends TestCase
{
    public function testItHandles(): void
    {
        $handlerMock = $this->createMock(RequestHandlerStrategy::class);
        $handlerMock->method('supports')->willReturn(true);
        $handlerMock->method('handle')->willReturn(['field' => 'asc']);

        $collection = new RequestHandlerCollection([$handlerMock]);

        $result = $collection->handle(new \stdClass(), ['field'], null);

        $this->assertEquals(['field' => 'asc'], $result);
    }

    public function testItThrowsIfNoHandler(): void
    {
        $this->expectException(NoHandlerException::class);
        $this->expectExceptionMessage('No handler found for request type: stdClass');

        $handlerMock = $this->createMock(RequestHandlerStrategy::class);
        $handlerMock->method('supports')->willReturn(false);

        $collection = new RequestHandlerCollection([$handlerMock]);
        $collection->handle(new \stdClass(), ['field'], null);
    }
}
