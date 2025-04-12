<?php

declare(strict_types=1);

namespace Rexpl\Struct\Contracts;

interface Rule
{
    public function validate(Source $source, string $key): bool;
}