<?php

declare(strict_types=1);

namespace YAR\Tests\Unit\Infrastructure\Persistence;

use PHPUnit\Framework\TestCase;
use YAR\Domain\Event;
use YAR\Domain\Filter;
use YAR\Domain\Subscription;
use YAR\Infrastructure\Persistence\InMemoryEventRepository;

final class InMemoryEventRepositoryTest extends TestCase
{
    public function testIt(): void
    {
        $sut = new InMemoryEventRepository();
        $sut->add(new Event(null, '0000000000000000000000000000000000000000000000000000000000000000', 1500000000, 0, [], 'YAR'));
        $sut->add(new Event(null, '1111111111111111111111111111111111111111111111111111111111111111', 1500000000, 0, [], 'YAR'));

        self::assertSame(2, $sut->count());
        self::assertCount(1, $sut->query(new Subscription('1234', 0, new Filter(authors: ['0000']))));
    }
}
