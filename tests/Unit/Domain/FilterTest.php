<?php

declare(strict_types=1);

namespace YAR\Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use YAR\Domain\Event;
use YAR\Domain\Filter;

final class FilterTest extends TestCase
{
    public function testEmptyFilter(): void
    {
        self::assertInstanceOf(Filter::class, new Filter());
    }

    public function testMatching(): void
    {
        $e1 = Event::create('35349f19bc4d11c427d1bf4eaa40e5755240e147a624578d9db43390fd63e8c3', 1500000000, 0, [], 'YAR');

        self::assertSame('e0aa3d76a05337a9e4e8fc6e92741ce0e1e3522f5d53a59be071deba19739577', $e1->id);

        self::assertTrue((new Filter())->matches($e1));
        self::assertTrue((new Filter(ids: ['e0aa']))->matches($e1));
        self::assertFalse((new Filter(ids: ['aaa']))->matches($e1));
        self::assertTrue((new Filter(authors: ['ce']))->matches($e1));
        self::assertFalse((new Filter(authors: ['01']))->matches($e1));

        $e2 = Event::create(
            '259dde0ba9dd8b5271dbcff42ed7951904f4d1222642404cefcf946c3f011300',
            1500000000,
            0,
            [
                ['e', 'e0aa3d76a05337a9e4e8fc6e92741ce0e1e3522f5d53a59be071deba19739577'],
                ['p', 'cef7d0f7ce50d1d5d5001b272c7d81fcd4cdb1cc983b12b0703804dfcc839a09'],
            ],
            'YAR'
        );

        self::assertTrue((new Filter(e: ['e0aa3d76a05337a9e4e8fc6e92741ce0e1e3522f5d53a59be071deba19739577']))->matches($e2));
        self::assertFalse((new Filter(e: ['0000000000000000000000000000000000000000000000000000000000000000']))->matches($e2));

        self::assertTrue((new Filter(p: ['cef7d0f7ce50d1d5d5001b272c7d81fcd4cdb1cc983b12b0703804dfcc839a09']))->matches($e2));
        self::assertFalse((new Filter(p: ['1111111111111111111111111111111111111111111111111111111111111111']))->matches($e2));
    }
}
