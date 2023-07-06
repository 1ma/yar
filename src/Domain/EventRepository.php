<?php

declare(strict_types=1);

namespace YAR\Domain;

interface EventRepository
{
    public function add(Event $event): void;

    /**
     * @return Event[]
     */
    public function query(Subscription $subscription): array;
}
