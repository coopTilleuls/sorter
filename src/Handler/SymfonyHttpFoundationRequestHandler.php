<?php

namespace Sorter\Handler;

use Sorter\Exception\PackageMissingException;
use Symfony\Component\HttpFoundation\Request;

class SymfonyHttpFoundationRequestHandler implements RequestHandlerStrategy
{
    #[\Override]
    public function handle(mixed $request, array $sorterFields, ?string $prefix): array
    {
        if (null !== $prefix) {
            parse_str($prefix, $result);
            $key = array_key_first($result);

            if (\is_string($key)) {
                /** @psalm-suppress MixedArgumentTypeCoercion */
                return [$key => $request->query->all($key)];
            }
        }

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
