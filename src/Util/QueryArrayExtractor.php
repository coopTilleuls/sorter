<?php

namespace Sorter\Util;

use Sorter\Exception\CannotExtractException;

final class QueryArrayExtractor
{
    /**
     * @param array<string, string>|array<string, array<string, string>>|array<string, array<string, array<string, string>>> $queryArray
     */
    public static function extract(?array $queryArray, ?string $prefix): array
    {
        if (null === $prefix) {
            return $queryArray ?? [];
        }

        return array_reduce(
            self::extractFullPathFromPrefix($prefix),
            static function (array $carry, string $part): array {
                /** @psalm-suppress MixedReturnStatement */
                return $carry[$part] ?? [];
            },
            $queryArray ?? [],
        );
    }

    /**
     * @return string[]
     */
    public static function extractFullPathFromPrefix(?string $prefix): array
    {
        if (null === $prefix) {
            return [];
        }

        $matches = [];
        if (!(preg_match('#^(?<outer>[^\[\]]+)(\[([^\[\]]+)])*$#', $prefix, $matches) > 0)) {
            throw new CannotExtractException('Invalid prefix format: '.$prefix);
        }

        $prefixParts = [$matches['outer']];
        if (preg_match_all('#\[(?<enclosed>[^\[\]]+)]#', $prefix, $matches) > 0) {
            $prefixParts = [...$prefixParts, ...$matches['enclosed']];
        }

        return $prefixParts;
    }
}
