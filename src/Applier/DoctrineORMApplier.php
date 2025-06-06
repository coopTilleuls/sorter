<?php

declare(strict_types=1);

namespace Sorter\Applier;

use Doctrine\ORM\QueryBuilder;
use Sorter\Exception\IncompatibleApplierException;
use Sorter\Sort;

/**
 * @implements SortApplier<QueryBuilder>
 */
final class DoctrineORMApplier implements SortApplier
{
    #[\Override]
    public function apply(Sort $sort, mixed $data, array $options = []): mixed
    {
        if (!$data instanceof QueryBuilder) {
            throw new IncompatibleApplierException(QueryBuilder::class, $data);
        }

        $override = filter_var($options['override'] ?? true, \FILTER_VALIDATE_BOOL);

        foreach ($sort->getFields() as $i => $field) {
            $data->{(0 === $i && $override) ? 'orderBy' : 'addOrderBy'}($sort->getPath($field), $sort->getDirection($field));
        }

        return $data;
    }

    #[\Override]
    public function supports(mixed $data): bool
    {
        return $data instanceof QueryBuilder;
    }
}
