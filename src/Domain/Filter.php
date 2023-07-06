<?php

declare(strict_types=1);

namespace YAR\Domain;

final class Filter
{
    private const DEFAULT_LIMIT = 100;

    /** @var string[]|null */
    public readonly ?array $ids;

    /** @var string[]|null */
    public readonly ?array $authors;

    /** @var string[]|null */
    public readonly ?array $kinds;

    /** @var string[]|null */
    public readonly ?array $e;

    /** @var string[]|null */
    public readonly ?array $p;

    public readonly ?int $since;
    public readonly ?int $until;
    public readonly int $limit;

    public function __construct(array $ids = null, array $authors = null, array $kinds = null, array $e = null, array $p = null, int $since = null, int $until = null, int $limit = null)
    {
        $this->ids = $ids;
        $this->authors = $authors;
        $this->kinds = $kinds;
        $this->e = $e;
        $this->p = $p;
        $this->since = $since;
        $this->until = $until;
        $this->limit = $limit ?? self::DEFAULT_LIMIT;
    }

    /**
     * Implements NIP-01 filtering logic.
     */
    public function matches(Event $event): bool
    {
        return $this->matchIds($event)
            && $this->matchAuthors($event)
            && $this->matchKinds($event)
            && $this->matchETags($event)
            && $this->matchPTags($event)
            && $this->matchSince($event)
            && $this->matchUntil($event);
    }

    private function matchIds(Event $event): bool
    {
        if (!\is_array($this->ids)) {
            return true;
        }

        foreach ($this->ids as $id) {
            if (str_starts_with($event->id, $id)) {
                return true;
            }
        }

        return false;
    }

    private function matchAuthors(Event $event): bool
    {
        if (!\is_array($this->authors)) {
            return true;
        }

        foreach ($this->authors as $author) {
            if (str_starts_with($event->publicKey, $author)) {
                return true;
            }
        }

        return false;
    }

    private function matchKinds(Event $event): bool
    {
        return !\is_array($this->kinds) || \in_array($event->kind, $this->kinds, true);
    }

    private function matchETags(Event $event): bool
    {
        if (!\is_array($this->e)) {
            return true;
        }

        foreach ($this->e as $e) {
            foreach ($event->tags as $tag) {
                if ('e' === $tag[0] && $tag[1] === $e) {
                    return true;
                }
            }
        }

        return false;
    }

    private function matchPTags(Event $event): bool
    {
        if (!\is_array($this->p)) {
            return true;
        }

        foreach ($this->p as $p) {
            foreach ($event->tags as $tag) {
                if ('p' === $tag[0] && $tag[1] === $p) {
                    return true;
                }
            }
        }

        return false;
    }

    private function matchSince(Event $event): bool
    {
        return !\is_int($this->since) || $this->since <= $event->createdAt;
    }

    private function matchUntil(Event $event): bool
    {
        return !\is_int($this->until) || $event->createdAt <= $this->until;
    }
}
