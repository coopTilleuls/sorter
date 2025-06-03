<?php

declare(strict_types=1);

namespace Sorter;

use Sorter\Applier\SortApplier;
use Sorter\Exception\UnknowApplierException;

/**
 * @template TSortableData
 */
final class SorterFactory
{
    /**
     * @var SortApplier<TSortableData>[]
     */
    private readonly array $appliers;

    /**
     * @param SortApplier<TSortableData>[] $appliers
     */
    public function __construct(array $appliers)
    {
        $this->appliers = $appliers;
    }

    public function createSorter(?Definition $definition = null): Sorter
    {
        $sorter = new Sorter($this);
        if (null !== $definition) {
            $definition->buildSorter($sorter);
        }

        return $sorter;
    }

    /**
     * @return SortApplier<TSortableData>
     */
    public function getApplier(mixed $data): SortApplier
    {
        foreach ($this->appliers as $applier) {
            if ($applier->supports($data)) {
                return $applier;
            }
        }

        throw new UnknowApplierException();
    }
}
