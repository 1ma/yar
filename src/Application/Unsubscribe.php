<?php

declare(strict_types=1);

namespace YAR\Application;

use YAR\Domain\Subscription;
use YAR\Domain\SubscriptionRepository;

final class Unsubscribe
{
    private readonly SubscriptionRepository $repository;

    public function __construct(SubscriptionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(Subscription $subscription): void
    {
        $this->repository->delete($subscription);
    }
}
