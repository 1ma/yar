<?php

declare(strict_types=1);

namespace YAR\Domain;

final class KeyPair
{
    public readonly string $privateKey;
    public readonly string $publicKey;

    public function __construct(string $privateKey)
    {
        $this->privateKey = $privateKey;
        $this->publicKey = secp256k1_nostr_derive_pubkey($privateKey);
    }

    /**
     * @throws \Exception
     */
    public static function generate(): self
    {
        return new self(bin2hex(random_bytes(32)));
    }
}
