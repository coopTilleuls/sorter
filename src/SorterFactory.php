<?php

declare(strict_types=1);

namespace Sorter;

use Sorter\Applier\SortApplier;
use Sorter\Exception\UnknowApplierException;
use Sorter\Handler\RequestHandler;
use Sorter\Handler\SymfonyHttpFoundationRequestHandler;

/**
 * @template TSortableData
 */
final class SorterFactory
{
    /**
     * @param SortApplier<TSortableData>[] $appliers
     */
    public function __construct(
        private readonly array $appliers,
        private readonly RequestHandler $requestHandler = new SymfonyHttpFoundationRequestHandler(),
    )
    {
    }

    public function createSorter(?Definition $definition = null): Sorter
    {
        $sorter = new Sorter($this, $this->requestHandler);
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
