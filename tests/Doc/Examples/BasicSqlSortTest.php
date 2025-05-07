<?php

namespace Sorter\Tests\Doc\Examples;

use PHPUnit\Framework\TestCase;

final class BasicSqlSortTest extends TestCase
{
    public function testItOutputsExpectedResult(): void
    {
        ob_start();
        require __DIR__.'/../../../examples/basic-sql-sort.php';
        $result = ob_get_clean();

        $this->assertSame(file_get_contents(__FILE__, offset: __COMPILER_HALT_OFFSET__), $result);
    }
}

__halt_compiler();

 Default sort (Ascending date):
 SELECT p, COUNT(p.comments) FROM post AS `p` INNER JOIN p.comments AS `comments` GROUP BY p.id ORDER BY p.date ASC


 Single column sort (Ascending title):
 SELECT p, COUNT(p.comments) FROM post AS `p` INNER JOIN p.comments AS `comments` GROUP BY p.id ORDER BY p.title ASC


 Double column sort (Ascending Weight, Ascending title):
 SELECT p, COUNT(p.comments) FROM post AS `p` INNER JOIN p.comments AS `comments` GROUP BY p.id ORDER BY p.weight ASC, p.title ASC
