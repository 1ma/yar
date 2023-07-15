<?php

declare(strict_types=1);

namespace YAR\Domain;

/**
 * Wrapper around the builtin json functions to avoid typing the flags everywhere.
 */
final class JSON
{
    /**
     * @throws \JsonException
     */
    public static function encode(mixed $data): string
    {
        return json_encode($data, flags: \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE | \JSON_THROW_ON_ERROR);
    }

    /**
     * @throws \JsonException
     */
    public static function decode(string $text): mixed
    {
        return json_decode($text, flags: \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE | \JSON_THROW_ON_ERROR);
    }
}
