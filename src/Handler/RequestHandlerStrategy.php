<?php

declare(strict_types=1);

namespace Sorter\Handler;

interface RequestHandlerStrategy extends RequestHandler
{
    public function supports(mixed $request): bool;
}
