<?php

declare(strict_types=1);

namespace Rexpl\Struct\Contracts;

interface Source
{
    public function has(string $key): bool;

    public function get(string $key): mixed;
}