<?php

declare(strict_types=1);

namespace Sorter\Tests\Extension\Symfony\Fixtures\Controller;

use Sorter\Definition;
use Sorter\Sorter;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class DoublePrefixArraySortController extends AbstractArraySortController
{
    protected function getSorterDefinition(): Definition
    {
        $parentDefinition = parent::getSorterDefinition();

        return new class($parentDefinition) implements Definition {
            public function __construct(private readonly Definition $parentDefinition)
            {
            }

            public function buildSorter(Sorter $sorter): void
            {
                $this->parentDefinition->buildSorter($sorter);

                $sorter->setPrefix('prefix[second]');
            }
        };
    }
}
