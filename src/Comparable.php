<?php

declare(strict_types=1);

namespace Sorter;

/**
 * @template T of Comparable
 */
interface Comparable
{
    /**
     * @param Comparable<T> $other the object to compare with
     */
    public function compare(self $other): int;
}
