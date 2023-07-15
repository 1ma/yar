<?php

declare(strict_types=1);

namespace YAR\Domain;

final class Event implements \JsonSerializable
{
    public readonly string $id;
    public readonly string $publicKey;
    public readonly int $createdAt;
    public readonly int $kind;

    /** @var EventTag[] */
    public readonly array $tags;
    public readonly string $content;
    public readonly string $signature;

    public function __construct(string $id, string $publicKey, int $createdAt, int $kind, string $content, string $signature, EventTag ...$tags)
    {
        if ($id !== self::computeId($publicKey, $createdAt, $kind, $tags, $content)) {
            throw new \InvalidArgumentException('Invalid Event id');
        }

        if (!secp256k1_nostr_verify($publicKey, $id, $signature)) {
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
        $signature = secp256k1_nostr_sign($keyPair->privateKey, $id);

        $tags = array_map(fn (array $tag): EventTag => new EventTag($tag[0], ...\array_slice($tag, 1)), $tags);

        return new self($id, $keyPair->publicKey, $createdAt, $kind, $content, $signature, ...$tags);
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
        return hash('sha256', json_encode([0, $publicKey, $createdAt, $kind, $tags, $content], \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE));
    }
}
