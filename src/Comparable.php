<?php

namespace Sorter;

/**
 * @template T of Comparable
 */
interface Comparable
{
    /**
     * @param Comparable<T> $other The object to compare with.
     */
    public function compare(Comparable $other): int;
}
