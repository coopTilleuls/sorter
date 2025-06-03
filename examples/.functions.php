<?php

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\Generator\Generator as MockGenerator;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @param array{
 *       'id': int,
 *       'title': string,
 *       'date': \DateTimeImmutable,
 *       'weight': int,
 *       'comments': array<int, int>
 *   }[] $data
 */
function display_data(array $data): void
{
    if (0 === count($data)) {
        return;
    }

    $maxTitleLength = max(array_map(fn(string $t) => mb_strlen($t), array_column($data, 'title')));
    $horizontalLine = ' -------' . str_repeat('-', $maxTitleLength - mb_strlen('Title')) . "-----------------------------------" . PHP_EOL;

    echo $horizontalLine;
    echo ' | Title' . str_repeat(' ', $maxTitleLength - mb_strlen('Title')) . " | Date       | Weight | Comments |" . PHP_EOL;
    echo $horizontalLine;

    foreach ([...$data] as $row) {
        echo
            ' | ' .
            $row['title'] . str_repeat(' ', $maxTitleLength - mb_strlen($row['title'])) .
            " | " .
            $row['date']->format('Y-m-d') .
            " | " .
            $row['weight'] . str_repeat(' ', 6 - mb_strlen((string)$row['weight'])) .
            " | " .
            count($row['comments']) . str_repeat(' ', 8 - mb_strlen((string)count($row['comments']))) .
            " |" .
            PHP_EOL;
    }

    echo $horizontalLine;
}

/**
 * @return array{
 *        'id': int,
 *        'title': string,
 *        'date': \DateTimeImmutable,
 *        'weight': int,
 *        'comments': array<int, int>
 *    }[]
 */
function generate_data(): array
{
    return [
        ['id' => 1, 'title' => 'Id adfectus correptius veritatis valde placeat.', 'date' => new \DateTimeImmutable('2025-12-01'), 'weight' => 10, 'comments' => range(0, 1)],
        ['id' => 2, 'title' => 'Avarus claudeo utrum sollicito tergo.', 'date' => new \DateTimeImmutable('2025-11-01'), 'weight' => 100, 'comments' => range(0, 3)],
        ['id' => 3, 'title' => 'Cognatus decimus dicta consectetur tergum antepono.', 'date' => new \DateTimeImmutable('2025-10-01'), 'weight' => 20, 'comments' => range(0, 5)],
        ['id' => 4, 'title' => 'Ter absconditus valetudo depono ad decipio summopere.', 'date' => new \DateTimeImmutable('2025-09-01'), 'weight' => 30, 'comments' => range(0, 7)],
        ['id' => 5, 'title' => 'Modi nihil accedo ut id sollers.', 'date' => new \DateTimeImmutable('2025-08-01'), 'weight' => 70, 'comments' => range(0, 9)],
        ['id' => 6, 'title' => 'Ventito suffragium caries vel paens omnis.', 'date' => new \DateTimeImmutable('2025-07-01'), 'weight' => 40, 'comments' => range(0, 11)],
        ['id' => 7, 'title' => 'Abstergo teneo vox nihil taedium admoneo deinde.', 'date' => new \DateTimeImmutable('2025-06-01'), 'weight' => 40, 'comments' => range(0, 12)],
        ['id' => 8, 'title' => 'Triduana thesis trepide adipiscor maiores subiungo sono rerum sui.', 'date' => new \DateTimeImmutable('2025-05-01'), 'weight' => 40, 'comments' => range(0, 10)],
        ['id' => 9, 'title' => 'Asper arx baiulus adnuo velit depereo fuga tempore decor.', 'date' => new \DateTimeImmutable('2025-04-01'), 'weight' => 100, 'comments' => range(0, 8)],
        ['id' => 10, 'title' => 'Angustus nobis auditor pecco.', 'date' => new \DateTimeImmutable('2025-03-01'), 'weight' => 0, 'comments' => range(0, 6)],
        ['id' => 11, 'title' => 'Ventito suffragium caries vel paens omnis.', 'date' => new \DateTimeImmutable('2025-02-01'), 'weight' => 10, 'comments' => range(0, 4)],
        ['id' => 12, 'title' => 'Colo contego possimus tabgo cunabula agnitio capio delectatio vivo.', 'date' => new \DateTimeImmutable('2025-01-01'), 'weight' => 10, 'comments' => range(0, 2)],
    ];
}

/**
 * @psalm-suppress InternalMethod
 */
function create_query_builder(): QueryBuilder {
    /** @var EntityManagerInterface&MockObject $em */
    $em = (new MockGenerator())->testDouble(
        EntityManagerInterface::class,
        true,
        callOriginalConstructor: false,
        callOriginalClone: false,
        cloneArguments: false,
        allowMockingUnknownTypes: false,
    );

    return new QueryBuilder($em);
}
