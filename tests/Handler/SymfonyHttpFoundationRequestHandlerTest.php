<?php

namespace Sorter\Tests\Handler;

use Sorter\Handler\SymfonyHttpFoundationRequestHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;

final class SymfonyHttpFoundationRequestHandlerTest extends TestCase
{
    private SymfonyHttpFoundationRequestHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new SymfonyHttpFoundationRequestHandler();
    }

    public function testItHandleSimpleRequest(): void
    {
        $request = $this->createMock(Request::class);
        $request->query = new InputBag(['a' => 'ASC']);

        $this->handler->handle($request, ['a'], null);

        $this->assertSame(['a' => 'ASC'], $this->handler->handle($request, ['a'], null));
    }

    public function testItHandleRequestWithPrefix(): void
    {
        $request = $this->createMock(Request::class);
        $request->query = new InputBag(['prefix' => ['a' => 'ASC'], 'other' => 'value']);

        $this->handler->handle($request, ['a'], null);

        $this->assertSame(['prefix' => ['a' => 'ASC']], $this->handler->handle($request, ['a'], 'prefix'));
    }

    public function testItHandleRequestWithDoublePrefix(): void
    {
        $request = $this->createMock(Request::class);
        $request->query = new InputBag(['prefix' => ['second_prefix' => ['a' => 'ASC'], 'other' => 'value'], 'other' => 'value']);

        $this->handler->handle($request, ['a'], null);

        $this->assertSame(
            ['a' => 'ASC'],
            $this->handler->handle($request, ['a'], 'prefix[second_prefix]')['prefix']['second_prefix'],
        );
    }

    public function testItSupports(): void
    {
        $this->assertTrue($this->handler->supports($this->createMock(Request::class)));
        $this->assertFalse($this->handler->supports(new \stdClass()));
    }
}
