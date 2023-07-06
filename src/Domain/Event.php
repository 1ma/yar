<?php

declare(strict_types=1);

namespace YAR\Domain;

use Mdanter\Ecc\Crypto\Signature\SchnorrSignature;

final class Event implements \JsonSerializable
{
    public readonly string $id;
    public readonly string $publicKey;
    public readonly int $createdAt;
    public readonly int $kind;
    public readonly array $tags;
    public readonly string $content;
    public readonly string $signature;

    public function __construct(string $id, string $publicKey, int $createdAt, int $kind, array $tags, string $content, string $signature)
    {
        if ($id !== self::computeId($publicKey, $createdAt, $kind, $tags, $content)) {
            throw new \InvalidArgumentException('Invalid Event id');
        }

        if (!self::verifySignature($publicKey, $signature, $id)) {
            throw new \InvalidArgumentException('Invalid Event signature');
        }

        $this->id = $id;
        $this->publicKey = $publicKey;
        $this->createdAt = $createdAt;
        $this->kind = $kind;
        $this->tags = $tags;
        $this->content = $content;
        $this->signature = $signature;
    }

    public static function create(string $privateKey, int $createdAt, int $kind, array $tags, string $content): self
    {
        $keyPair = new KeyPair($privateKey);

        $id = self::computeId($keyPair->publicKey, $createdAt, $kind, $tags, $content);
        $signature = (new SchnorrSignature())->sign($keyPair->privateKey, $id)['signature'];

        return new self($id, $keyPair->publicKey, $createdAt, $kind, $tags, $content, $signature);
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'pubkey' => $this->publicKey,
            'created_at' => $this->createdAt,
            'kind' => $this->kind,
            'tags' => $this->tags,
            'content' => $this->content,
            'sig' => $this->signature,
        ];
    }

    private static function computeId(string $publicKey, int $createdAt, int $kind, array $tags, string $content): string
    {
        return hash('sha256', json_encode([0, $publicKey, $createdAt, $kind, $tags, $content]));
    }

    private static function verifySignature(string $publicKey, string $signature, string $message): bool
    {
        return (new SchnorrSignature())->verify($publicKey, $signature, $message);
    }
}
