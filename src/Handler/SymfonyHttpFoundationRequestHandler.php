<?php

declare(strict_types=1);

namespace Sorter\Handler;

use Sorter\Exception\PackageMissingException;
use Symfony\Component\HttpFoundation\Request;

final class SymfonyHttpFoundationRequestHandler implements RequestHandlerStrategy
{
    /**
     * @psalm-suppress MixedReturnTypeCoercion
     */
    #[\Override]
    public function handle(mixed $request, array $sorterFields, ?string $prefix): array
    {
        \assert($request instanceof Request);

        if (null !== $prefix) {
            parse_str($prefix, $result);
            $key = array_key_first($result);

            if (\is_string($key)) {
                return [$key => $request->query->all($key)];
            }
        }

        /**
         * @var array<string, string> $fields
         */
        $fields = [];
        foreach ($sorterFields as $field) {
            if (null !== ($value = $request->query->get($field))) {
                $fields[$field] = (string) $value;
            }
        }

        return $fields;
    }

    #[\Override]
    public function supports(mixed $request): bool
    {
        if (!class_exists(Request::class)) {
            // @codeCoverageIgnoreStart
            // @infection-ignore-all
            throw new PackageMissingException('symfony/http-foundation', __CLASS__);
            // @codeCoverageIgnoreEnd
        }

        return $request instanceof Request;
    }
}
