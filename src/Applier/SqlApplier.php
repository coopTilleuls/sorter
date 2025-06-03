<?php

declare(strict_types=1);

namespace Sorter\Applier;

use PhpMyAdmin\SqlParser\Components\Expression;
use PhpMyAdmin\SqlParser\Components\OrderKeyword;
use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Statements\SelectStatement;
use Sorter\Exception\IncompatibleApplierException;
use Sorter\Exception\IncompatibleQueryException;
use Sorter\Exception\PackageMissingException;
use Sorter\Sort;

/**
 * @implements SortApplier<string>
 */
final class SqlApplier implements SortApplier
{
    #[\Override]
    public function apply(Sort $sort, mixed $data, array $options = []): mixed
    {
        if (!class_exists(Parser::class)) {
            // @codeCoverageIgnoreStart
            // @infection-ignore-all
            throw new PackageMissingException('phpmyadmin/sql-parser', __CLASS__);
            // @codeCoverageIgnoreEnd
        }

        if (!$this->supports($data)) {
            throw new IncompatibleApplierException('SQL String', $data);
        }

        $parser = new Parser($data, false);
        $selectStatement = $parser->statements[0];
        if (!$selectStatement instanceof SelectStatement) {
            throw new IncompatibleQueryException();
        }

        foreach ($sort->getFields() as $field) {
            $path = $sort->getPath($field);
            $newOrderKeyword = new OrderKeyword(new Expression($path), strtoupper($sort->getDirection($field)));

            foreach ($selectStatement->order ?? [] as $i => $orderKeyword) {
                if ($newOrderKeyword->expr->expr === $orderKeyword->expr->expr) {
                    $selectStatement->order[$i] = $newOrderKeyword;

                    continue 2;
                }
            }

            $selectStatement->order[] = $newOrderKeyword;
        }

        return $selectStatement->build();
    }

    #[\Override]
    public function supports(mixed $data): bool
    {
        if (!\is_string($data)) {
            return false;
        }

        $parser = new Parser($data, false);

        return 0 !== \count($parser->statements);
    }
}
