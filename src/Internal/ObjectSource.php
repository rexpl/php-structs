<?php

declare(strict_types=1);

namespace Rexpl\Struct\Internal;

use Rexpl\Struct\Contracts\Source;

readonly class ObjectSource implements Source
{
    public function __construct(private object $data) {}

    public function has(string $key): bool
    {
        return \property_exists($this->data, $key);
    }

    public function get(string $key): mixed
    {
        return $this->data->{$key};
    }
}