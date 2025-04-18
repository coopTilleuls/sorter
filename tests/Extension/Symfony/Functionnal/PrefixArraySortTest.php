<?php

declare(strict_types=1);

namespace Sorter\Tests\Extension\Symfony\Functionnal;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PrefixArraySortTest extends WebTestCase
{
    #[DataProvider('providesSortParamsAndTitle')]
    public function testItDisplaySortedTable(string $queryString, array $titles, array $links, string $clickedLink, string $expectedUrl): void
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/array-sort-prefix' . $queryString);
        $this->assertResponseIsSuccessful();

        foreach ($titles as $i => $title) {
            $this->assertStringContainsString(
                $title,
                $crawler->filter('tbody > tr:nth-child('.($i + 1).')')->text(),
            );
        }

        foreach ($links as $column => $order) {
            $this->assertStringContainsString(
                '?'.urlencode('prefix['.$column.']').'='.$order,
                $crawler->filter('thead th[data-col='.$column.'] a')->attr('href'),
            );
        }

        $client->clickLink($clickedLink);
        $this->assertResponseIsSuccessful();
        $this->assertSame($expectedUrl, $client->getRequest()->getRequestUri());
    }

    public static function providesSortParamsAndTitle(): iterable
    {
        yield 'Default order' => [
            '',
            [
                'The third title',
                'The fourth title',
                'The second title',
                'The fifth title',
                'The first title',
            ],
            ['c' => 'DESC', 'a' => 'ASC'],
            'C',
            '/array-sort-prefix?prefix%5Bc%5D=DESC',
        ];

        yield 'Title Ascending (First Column)' => [
            '?prefix[title]=ASC',
            [
                'The fifth title',
                'The first title',
                'The fourth title',
                'The second title',
                'The third title',
            ],
            ['title' => 'DESC', 'a' => 'ASC'],
            'Title',
            '/array-sort-prefix?prefix%5Btitle%5D=DESC',
        ];

        yield 'Title Descending (First Column)' => [
            '?prefix[title]=DESC',
            [
                'The third title',
                'The second title',
                'The fourth title',
                'The first title',
                'The fifth title',
            ],
            ['title' => 'ASC'],
            'Title',
            '/array-sort-prefix?prefix%5Btitle%5D=ASC',
        ];

        yield 'Integer column Ascending' => [
            '?prefix[a]=ASC',
            [
                'The first title',
                'The second title',
                'The third title',
                'The fourth title',
                'The fifth title',
            ],
            ['a' => 'DESC'],
            'C',
            '/array-sort-prefix?prefix%5Bc%5D=ASC',
        ];
    }


    #[DataProvider('providesSortParamsAndTitleWithDoublePrefix')]
    public function testItDisplaySortedTableWithDoublePrefix(string $queryString, array $titles, array $links, string $clickedLink, string $expectedUrl): void
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/array-sort-double-prefix' . $queryString);
        $this->assertResponseIsSuccessful();

        foreach ($titles as $i => $title) {
            $this->assertStringContainsString(
                $title,
                $crawler->filter('tbody > tr:nth-child('.($i + 1).')')->text(),
            );
        }

        foreach ($links as $column => $order) {
            $this->assertStringContainsString(
                '?'.urlencode('prefix[second]['.$column.']').'='.$order,
                $crawler->filter('thead th[data-col='.$column.'] a')->attr('href'),
            );
        }

        $client->clickLink($clickedLink);
        $this->assertResponseIsSuccessful();
        $this->assertSame($expectedUrl, $client->getRequest()->getRequestUri());
    }

    public static function providesSortParamsAndTitleWithDoublePrefix(): iterable
    {
        yield 'Default order (double prefix)' => [
            '',
            [
                'The third title',
                'The fourth title',
                'The second title',
                'The fifth title',
                'The first title',
            ],
            ['c' => 'DESC', 'a' => 'ASC'],
            'C',
            '/array-sort-double-prefix?prefix%5Bsecond%5D%5Bc%5D=DESC',
        ];

        yield 'Title Ascending (First Column) (double prefix)' => [
            '?prefix[second][title]=ASC',
            [
                'The fifth title',
                'The first title',
                'The fourth title',
                'The second title',
                'The third title',
            ],
            ['title' => 'DESC', 'a' => 'ASC'],
            'Title',
            '/array-sort-double-prefix?prefix%5Bsecond%5D%5Btitle%5D=DESC',
        ];

        yield 'Title Descending (First Column) (double prefix)' => [
            '?prefix[second][title]=DESC',
            [
                'The third title',
                'The second title',
                'The fourth title',
                'The first title',
                'The fifth title',
            ],
            ['title' => 'ASC'],
            'Title',
            '/array-sort-double-prefix?prefix%5Bsecond%5D%5Btitle%5D=ASC',
        ];

        yield 'Integer column Ascending (double prefix)' => [
            '?prefix[second][a]=ASC',
            [
                'The first title',
                'The second title',
                'The third title',
                'The fourth title',
                'The fifth title',
            ],
            ['a' => 'DESC'],
            'C',
            '/array-sort-double-prefix?prefix%5Bsecond%5D%5Bc%5D=ASC',
        ];
    }
}
