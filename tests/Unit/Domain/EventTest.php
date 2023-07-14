<?php

declare(strict_types=1);

namespace YAR\Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use YAR\Domain\Event;
use YAR\Domain\EventTag;

final class EventTest extends TestCase
{
    public function testIdComputation(): void
    {
        $event = Event::create('35349f19bc4d11c427d1bf4eaa40e5755240e147a624578d9db43390fd63e8c3', 1500000000, 0, [], 'YAR');

        self::assertSame('e0aa3d76a05337a9e4e8fc6e92741ce0e1e3522f5d53a59be071deba19739577', $event->id);
    }

    public function testVerifyRealEvent(): void
    {
        $event = new Event(
            'ce84bef1e5ceb60282a2bca14d70e9c3d6a6df53c9c8511617f8962cae2faeac',
            '7bf7db83f73228f5df6ba34849f2af9fd54bf565b5ad698ac708249b310079a0',
            1676670758,
            1,
            'Henlo fren.',
            'a7ef3fc9113d15ecc0874ad4555f5bf0446a2d0fdd1ccc07f62abc69934072d0a98917ec0807a8ff88a189d3184fa4f0c3820c868976975b6e87b8f11816b482',
            new EventTag('e', '0514eea667ff1c5ebe66c1790a0c36e2d9507c27c22e7e8de0798ab0712909d8'),
            new EventTag('p', '74ffc51cc30150cf79b6cb316d3a15cf332ab29a38fec9eb484ab1551d6d1856')
        );

        self::assertInstanceOf(Event::class, $event);
    }
}
