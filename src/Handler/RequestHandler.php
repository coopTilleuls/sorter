<?php

declare(strict_types=1);

namespace Sorter\Handler;

interface RequestHandler
{
    /**
     * @param string[] $sorterFields
     *
     * @return array<string, string|array<string, string>>
     */
    public function handle(mixed $request, array $sorterFields, ?string $prefix): array;
}
