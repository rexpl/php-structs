<?php

declare(strict_types=1);

namespace Rexpl\Struct\Internal;

use Rexpl\Struct\Contracts\Source;

readonly class ArraySource implements Source
{
    public function __construct(private array $data) {}

    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->data);
    }

    public function get(string $key): mixed
    {
        return $this->data[$key];
    }
}