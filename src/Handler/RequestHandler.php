<?php

namespace Sorter\Handler;

interface RequestHandler
{
    public function handle(mixed $request, array $sorterFields, ?string $prefix): array;
}
