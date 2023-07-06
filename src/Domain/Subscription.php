<?php

declare(strict_types=1);

namespace YAR\Domain;

final class Subscription
{
    public readonly string $id;
    public readonly int $clientId;

    /** @var Filter[] */
    public readonly array $filters;

    public function __construct(string $id, int $clientId, Filter ...$filters)
    {
        $this->id = $id;
        $this->clientId = $clientId;
        $this->filters = $filters;
    }
}
