<?php

declare(strict_types=1);

namespace YAR\Domain;

final class Event implements \JsonSerializable
{
    public readonly string $id;
    public readonly string $publicKey;
    public readonly int $createdAt;
    public readonly int $kind;
    public readonly array $tags;
    public readonly string $content;

    public function __construct(?string $id, string $publicKey, int $createdAt, int $kind, array $tags, string $content)
    {
        $this->publicKey = $publicKey;
        $this->createdAt = $createdAt;
        $this->kind = $kind;
        $this->tags = $tags;
        $this->content = $content;

        $this->id = $id ?? hash('sha256', json_encode([0, $this->publicKey, $this->createdAt, $this->kind, $this->tags, $this->content]));
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
        ];
    }
}
