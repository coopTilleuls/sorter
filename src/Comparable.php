<?php

declare(strict_types=1);

namespace Sorter;

/**
 * @template T of Comparable
 */
interface Comparable
{
    /**
     * @param T $other the object to compare with
     */
    public function compare(Comparable $other): int;
}
