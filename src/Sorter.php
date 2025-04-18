<?php

declare(strict_types=1);

namespace Sorter;

use Sorter\Exception\NoSortException;
use Sorter\Exception\ScalarExpectedException;
use Sorter\Exception\UnknowSortDirectionException;
use Sorter\Util\QueryArrayExtractor;
use Symfony\Component\HttpFoundation\Request;

final class Sorter
{
    /**
     * @var array<string, string>
     */
    private array $fields = [];

    /**
     * @var array<string, Sort::ASC|Sort::DESC>
     */
    private array $defaults = [];

    private ?Sort $currentSort = null;

    private ?string $prefix = null;

    public function __construct(private readonly SorterFactory $factory)
    {
    }

    public function add(string $field, string $path): self
    {
        $this->fields[$field] = $path;

        return $this;
    }

    public function addDefault(string $path, string $direction): self
    {
        if (!\in_array($direction, [Sort::ASC, Sort::DESC], true)) {
            throw new UnknowSortDirectionException($direction);
        }

        $this->defaults[$path] = $direction;

        return $this;
    }

    public function removeDefault(string $path): self
    {
        if (isset($this->defaults[$path])) {
            unset($this->defaults[$path]);
        }

        return $this;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function setPrefix(?string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getFields(): array
    {
        return array_keys($this->fields);
    }

    public function getPath(string $field): string
    {
        return $this->fields[$field];
    }

    public function getCurrentSort(): Sort
    {
        if (null === $this->currentSort) {
            throw new NoSortException();
        }

        return $this->currentSort;
    }

    /**
     * @param array<string, string|array<string, string>> $values
     */
    public function handle(array $values): void
    {
        if (null !== $this->prefix) {
            $values = QueryArrayExtractor::extract($values, $this->prefix);
        }

        $sort = new Sort();
        /** @var array<string, mixed> $values */
        foreach ($values as $field => $value) {
            if (!\is_scalar($value)) {
                throw new ScalarExpectedException($value);
            }

            if (!\in_array($value, [Sort::ASC, Sort::DESC], true)) {
                throw new UnknowSortDirectionException($value);
            }

            $sort->add($field, $this->getPath($field), $value);
        }

        if (0 === \count($sort->getFields())) {
            foreach ($this->defaults as $field => $direction) {
                $sort->add($field, $this->getPath($field), $direction);
            }
        }

        $this->currentSort = $sort;
    }

    public function handleRequest(Request $request): void
    {
        if (null !== $this->prefix) {
            parse_str($this->prefix, $result);
            $key = array_key_first($result);

            if (\is_string($key)) {
                /** @psalm-suppress MixedArgumentTypeCoercion */
                $this->handle([$key => $request->query->all($key)]);
            }

            return;
        }

        $fields = [];
        foreach ($this->getFields() as $field) {
            if ($request->query->has($field) && $value = $request->query->getString($field)) {
                $fields[$field] = $value;
            }
        }

        $this->handle($fields);
    }

    public function sort(mixed $data, array $options = []): mixed
    {
        return $this->factory->getApplier($data)->apply($this->getCurrentSort(), $data, $options);
    }
}
