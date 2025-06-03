<?php

declare(strict_types=1);

namespace Sorter\Applier;

use Sorter\Sort;

/**
 * @template TSortableData
 */
interface SortApplier
{
    /**
     * @param TSortableData $data
     *
     * @return TSortableData
     */
    public function apply(Sort $sort, mixed $data, array $options = []): mixed;

    public function supports(mixed $data): bool;
}
