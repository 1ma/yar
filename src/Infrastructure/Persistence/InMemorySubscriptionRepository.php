<?php

declare(strict_types=1);

namespace YAR\Infrastructure\Persistence;

use YAR\Domain\Event;
use YAR\Domain\Subscription;
use YAR\Domain\SubscriptionRepository;

final class InMemorySubscriptionRepository implements SubscriptionRepository
{
    /** @var Subscription[] */
    private array $subscriptions;

    public function __construct()
    {
        $this->subscriptions = [];
    }

    public function register(Subscription $subscription): void
    {
        $this->subscriptions[$subscription->clientId][$subscription->id] = $subscription;
    }

    public function delete(Subscription $subscription): void
    {
        if (isset($this->subscriptions[$subscription->clientId][$subscription->id])) {
            unset($this->subscriptions[$subscription->clientId][$subscription->id]);
        }
    }

    public function deleteAll(int $clientId): void
    {
        if (isset($this->subscriptions[$clientId])) {
            unset($this->subscriptions[$clientId]);
        }
    }

    public function matches(Event $event): array
    {
        $matches = [];
        foreach ($this->subscriptions as $subscriptions) {
            foreach ($subscriptions as $subscription) {
                foreach ($subscription->filters as $filter) {
                    if ($filter->matches($event)) {
                        $matches[] = $subscription;
                        break;
                    }
                }
            }
        }

        return $matches;
    }
}
