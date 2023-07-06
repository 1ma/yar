<?php

declare(strict_types=1);

namespace YAR\Domain;

interface SubscriptionRepository
{
    public function register(Subscription $subscription): void;

    public function delete(Subscription $subscription): void;

    public function deleteAll(int $clientId): void;

    /**
     * @return Subscription[]
     */
    public function matches(Event $event): array;
}
