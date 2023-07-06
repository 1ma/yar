<?php

declare(strict_types=1);

namespace YAR\Infrastructure\Persistence;

use YAR\Domain\Event;
use YAR\Domain\EventRepository;
use YAR\Domain\Subscription;

final class InMemoryEventRepository implements EventRepository
{
    /**
     * @var Event[]
     */
    private array $events;

    public function __construct()
    {
        $this->events = [];
    }

    public function add(Event $event): void
    {
        $this->events[$event->id] = $event;
    }

    public function query(Subscription $subscription): array
    {
        $matches = [];
        foreach ($subscription->filters as $filter) {
            $matches = [...$matches, ...array_filter($this->events, fn (Event $event): bool => $filter->matches($event))];
        }

        // TODO NIP-01: implement limit

        return array_values($matches);
    }

    public function count(): int
    {
        return \count($this->events);
    }
}
