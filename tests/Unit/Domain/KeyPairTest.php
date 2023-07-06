<?php

declare(strict_types=1);

namespace YAR\Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use YAR\Domain\KeyPair;

final class KeyPairTest extends TestCase
{
    public function testPublicKeyCorrectness(): void
    {
        self::assertSame(
            'cef7d0f7ce50d1d5d5001b272c7d81fcd4cdb1cc983b12b0703804dfcc839a09',
            (new KeyPair('35349f19bc4d11c427d1bf4eaa40e5755240e147a624578d9db43390fd63e8c3'))->publicKey
        );
    }
}
