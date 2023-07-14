<?php

declare(strict_types=1);

namespace YAR\Domain;

final class EventTag implements \JsonSerializable
{
    public readonly string $name;

    /** @var string[] */
    public readonly array $values;

    public function __construct(string $name, string ...$values)
    {
        $this->name = $name;
        $this->values = $values;
    }

    public function jsonSerialize(): mixed
    {
        return [
            $this->name,
            ...$this->values,
        ];
    }
}
