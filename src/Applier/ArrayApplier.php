<?php

declare(strict_types=1);

namespace Sorter\Applier;

use Sorter\Comparable;
use Sorter\Exception\IncompatibleApplierException;
use Sorter\Exception\PackageMissingException;
use Sorter\Sort;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * @template TSortableData
 *
 * @implements SortApplier<TSortableData>
 */
final class ArrayApplier implements SortApplier
{
    private readonly PropertyAccessor $propertyAccessor;

    public function __construct(?PropertyAccessor $propertyAccessor = null)
    {
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
    }

    #[\Override]
    public function apply(Sort $sort, mixed $data, array $options = []): array
    {
        if (!class_exists(PropertyAccessor::class)) {
            // @codeCoverageIgnoreStart
            // @infection-ignore-all
            throw new PackageMissingException('symfony/property-access', __CLASS__);
            // @codeCoverageIgnoreEnd
        }

        if (!\is_array($data)) {
            throw new IncompatibleApplierException('array', $data);
        }

        /** @var list<array<string, mixed>|object> $data */
        usort(
            $data,
            /**
             * @param array<string, mixed>|object $left
             * @param array<string, mixed>|object $right
             */
            function ($left, $right) use ($sort) {
                foreach ($sort->getFields() as $field) {
                    $path = $sort->getPath($field);

                    if (!str_contains($path, '[') && \is_array($left)) {
                        $path = '['.$path.']';
                    }

                    /** @var mixed $leftValue */
                    $leftValue = $this->propertyAccessor->getValue($left, $path);
                    /** @var mixed $rightValue */
                    $rightValue = $this->propertyAccessor->getValue($right, $path);

                    if ($leftValue instanceof Comparable && $rightValue instanceof Comparable) {
                        return (Sort::ASC === $sort->getDirection($field) ? 1 : -1) * $leftValue->compare($rightValue);
                    }

                    if ($leftValue instanceof \BackedEnum && $rightValue instanceof \BackedEnum) {
                        $leftValue = $leftValue->value;
                        $rightValue = $rightValue->value;
                    }

                    if ($leftValue > $rightValue) {
                        return Sort::ASC === $sort->getDirection($field) ? 1 : -1;
                    }

                    if ($leftValue < $rightValue) {
                        return Sort::ASC === $sort->getDirection($field) ? -1 : 1;
                    }
                }

                return 0;
            }
        );

        return $data;
    }

    #[\Override]
    public function supports(mixed $data): bool
    {
        return \is_array($data);
    }
}
