<?php

declare(strict_types=1);

namespace Sorter\Handler;

use Sorter\Exception\NoHandlerException;

final class RequestHandlerCollection implements RequestHandler
{
    /**
     * @param iterable<RequestHandlerStrategy> $handlers
     */
    public function __construct(
        private readonly iterable $handlers,
    ) {
    }

    #[\Override]
    public function handle(mixed $request, array $sorterFields, ?string $prefix): array
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($request)) {
                return $handler->handle($request, $sorterFields, $prefix);
            }
        }

        throw new NoHandlerException(\is_object($request) ? $request::class : \gettype($request));
    }
}
