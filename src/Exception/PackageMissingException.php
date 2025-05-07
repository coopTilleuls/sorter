<?php

declare(strict_types=1);

namespace Sorter\Exception;

/**
 * @codeCoverageIgnore This exception is thrown when a composer package is missing. It is not possible to test this.
 *
 * @infection-ignore-all
 */
final class PackageMissingException extends \RuntimeException implements SorterException
{
    public function __construct(string $package, string $featureRequiring)
    {
        parent::__construct(
            \sprintf(
                'The "%s" package is required to use the "%s" feature.',
                $package,
                $featureRequiring,
            ),
        );
    }
}
