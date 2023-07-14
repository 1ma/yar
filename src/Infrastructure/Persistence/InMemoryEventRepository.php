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
        array_unshift($this->events, $event);
    }

    public function query(Subscription $subscription): array
    {
        $matches = [];
        foreach ($subscription->filters as $filter) {
            $filterMatches = array_filter($this->events, fn (Event $event): bool => $filter->matches($event));
            $matches = [...$matches, ...\array_slice($filterMatches, 0, $filter->limit)];
        }

        return $matches;
    }

    public function count(): int
    {
        return \count($this->events);
    }
}
