<?php

declare(strict_types=1);

namespace YAR\Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use YAR\Domain\Event;

final class EventTest extends TestCase
{
    public function testIdComputation(): void
    {
        $event = new Event(null, '0000000000000000000000000000000000000000000000000000000000000000', 1500000000, 0, [], 'YAR');

        self::assertSame('1f37c24476491ac854e4107d58ae02d9e4784f05b78c5b402ceb120e005d3f13', $event->id);
    }
}
