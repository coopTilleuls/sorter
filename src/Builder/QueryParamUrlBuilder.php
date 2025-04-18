<?php

declare(strict_types=1);

namespace Sorter\Builder;

use Sorter\Sort;
use Sorter\Sorter;
use Sorter\Util\QueryArrayExtractor;
use Symfony\Component\HttpFoundation\Request;

final class QueryParamUrlBuilder implements UrlBuilder
{
    #[\Override]
    public function generateFromRequest(Sorter $sorter, Request $request, string $field, ?string $direction = null): string
    {
        if (null === $direction && $sorter->getCurrentSort()->has($field)) {
            $direction = Sort::ASC === $sorter->getCurrentSort()->getDirection($field) ? Sort::DESC : Sort::ASC;
        } elseif (null === $direction) {
            $direction = Sort::ASC;
        }

        $parsedUrl = parse_url($request->getUri());
        parse_str($parsedUrl['query'] ?? '', $query);
        /** @var array<string, string|array<string, string>> $query */

        $prefix = $sorter->getPrefix();
        foreach ($sorter->getFields() as $fieldName) {
            if (null === $prefix) {
                unset($query[$fieldName]);

                continue;
            }

            /** @var array<string, string|array<string, string>> $queryPart */
            $queryPart = &$query;
            foreach (QueryArrayExtractor::extractFullPathFromPrefix($prefix) as $path) {
                /** @var array<string, string|array<string, string>> $queryPart */
                $queryPart = &$queryPart[$path];
            }

            unset($queryPart[$fieldName]);
        }

        if (null === $prefix) {
            /** @var array<string, string> $query */
            $query[$field] = $direction;
        } else {
            /** @var array<string, array<string, string>> $query */
            $queryPart = &$query;
            foreach (QueryArrayExtractor::extractFullPathFromPrefix($prefix) as $path) {
                $queryPart = &$queryPart[$path];
            }

            $queryPart[$field] = $direction;
        }

        return ($parsedUrl['path'] ?? '').'?'.http_build_query($query);
    }
}
