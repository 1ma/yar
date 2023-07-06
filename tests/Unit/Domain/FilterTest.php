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
        $e1 = new Event(null, '0000000000000000000000000000000000000000000000000000000000000000', 1500000000, 0, [], 'YAR');

        self::assertSame('1f37c24476491ac854e4107d58ae02d9e4784f05b78c5b402ceb120e005d3f13', $e1->id);

        self::assertTrue((new Filter())->matches($e1));
        self::assertTrue((new Filter(ids: ['1f37']))->matches($e1));
        self::assertFalse((new Filter(ids: ['aaa']))->matches($e1));
        self::assertTrue((new Filter(authors: ['00']))->matches($e1));
        self::assertFalse((new Filter(authors: ['01']))->matches($e1));

        $e2 = new Event(
            null,
            '1111111111111111111111111111111111111111111111111111111111111111',
            1500000000,
            0,
            [
                ['#e', '1f37c24476491ac854e4107d58ae02d9e4784f05b78c5b402ceb120e005d3f13'],
                ['#p', '0000000000000000000000000000000000000000000000000000000000000000'],
            ],
            'YAR'
        );

        self::assertTrue((new Filter(e: ['1f37c24476491ac854e4107d58ae02d9e4784f05b78c5b402ceb120e005d3f13']))->matches($e2));
        self::assertFalse((new Filter(e: ['0000000000000000000000000000000000000000000000000000000000000000']))->matches($e2));

        self::assertTrue((new Filter(p: ['0000000000000000000000000000000000000000000000000000000000000000']))->matches($e2));
        self::assertFalse((new Filter(p: ['1111111111111111111111111111111111111111111111111111111111111111']))->matches($e2));
    }
}
