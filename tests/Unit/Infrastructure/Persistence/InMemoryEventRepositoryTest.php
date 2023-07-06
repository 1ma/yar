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
        $sut->add(Event::create('35349f19bc4d11c427d1bf4eaa40e5755240e147a624578d9db43390fd63e8c3', 1500000000, 0, [], 'YAR'));
        $sut->add(Event::create('259dde0ba9dd8b5271dbcff42ed7951904f4d1222642404cefcf946c3f011300', 1500000000, 0, [], 'YAR'));

        self::assertSame(2, $sut->count());
        self::assertCount(1, $sut->query(new Subscription('1234', 0, new Filter(authors: ['cef7']))));
    }
}
