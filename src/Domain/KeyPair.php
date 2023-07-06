<?php

declare(strict_types=1);

namespace YAR\Domain;

use Elliptic\EC;

final class KeyPair
{
    public string $privateKey;
    public string $publicKey;

    public function __construct(string $privateKey)
    {
        $this->privateKey = $privateKey;
        $this->publicKey = substr((new EC('secp256k1'))->keyFromPrivate($privateKey)->getPublic(true, 'hex'), 2);
    }

    public function __destruct()
    {
        $this->privateKey = '0000000000000000000000000000000000000000000000000000000000000000';
        $this->publicKey = '0000000000000000000000000000000000000000000000000000000000000000';
    }

    public static function generate(): self
    {
        return new self((new EC('secp256k1'))->genKeyPair()->priv->toString('hex'));
    }
}
