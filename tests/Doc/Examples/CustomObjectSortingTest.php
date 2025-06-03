<?php

namespace Sorter\Tests\Doc\Examples;

use PHPUnit\Framework\TestCase;

final class CustomObjectSortingTest extends TestCase
{
    public function testItOutputsExpectedResult(): void
    {
        ob_start();
        require __DIR__.'/../../../examples/custom-object-sorting.php';
        $result = ob_get_clean();

        $this->assertSame(file_get_contents(__FILE__, offset: __COMPILER_HALT_OFFSET__), $result);
    }
}

__halt_compiler();
 ---------------------
 | Value 1 | Value 2 |
 ---------------------
 |  x      |  c      |
 |  v      |  e      |
 |  y      |  b      |
 |  z      |  a      |
 |  w      |  d      |
 ---------------------
 ---------------------
 | Value 1 | Value 2 |
 ---------------------
 |  z      |  a      |
 |  y      |  b      |
 |  x      |  c      |
 |  w      |  d      |
 |  v      |  e      |
 ---------------------
