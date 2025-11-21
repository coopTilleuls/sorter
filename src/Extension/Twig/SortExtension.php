<?php

declare(strict_types=1);

namespace Sorter\Extension\Twig;

use Sorter\Builder\UrlBuilder;
use Sorter\Exception\NoRequestException;
use Sorter\Sorter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class SortExtension extends AbstractExtension
{
    public function __construct(
        private readonly UrlBuilder $urlBuilder,
        private readonly RequestStack $requestStack,
    ) {
    }

    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('sorter_link', $this->getSorterLink(...), ['is_safe' => ['html']]),
            new TwigFunction('sorter_url', $this->getSortUrl(...)),
            new TwigFunction('sorter_direction', $this->getSortDirection(...)),
        ];
    }

    public function getSortDirection(Sorter $sorter, string $field): ?string
    {
        $sort = $sorter->getCurrentSort();
        if (!$sort->has($field)) {
            return null;
        }

        return $sort->getDirection($field);
    }

    public function getSortUrl(Sorter $sorter, string $field, ?string $direction = null, ?Request $request = null): string
    {
        $request = $request ?: $this->requestStack->getCurrentRequest();
        if (null === $request) {
            throw new NoRequestException();
        }

        return $this->urlBuilder->generateFromRequest(
            $sorter,
            $request,
            $field,
            $direction,
        );
    }

    public function getSorterLink(
        Sorter $sorter,
        string $field,
        string $text,
        ?string $direction = null,
        ?Request $request = null,
        array $attributes = [],
    ): string {
        $url = $this->getSortUrl($sorter, $field, $direction, $request);

        $currentDirection = $this->getSortDirection($sorter, $field);
        if (null !== $currentDirection) {
            $attributes['data-current-sort'] = $currentDirection;
        }

        /** @var array<string, string> $attributes */
        $attributesStr = implode(' ', array_map(
            fn (string $key, string $value) => \sprintf('%s="%s"', $key, htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')),
            array_keys($attributes),
            $attributes,
        ));

        $attributesStr = $attributesStr ? ' '.$attributesStr : '';

        return strtr(
            '<a href="%url%"%attributes%>%text%</a>',
            [
                '%url%' => $url,
                '%text%' => $text,
                '%attributes%' => $attributesStr,
            ],
        );
    }
}
