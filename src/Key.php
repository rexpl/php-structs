<?php

declare(strict_types=1);

namespace Rexpl\Struct;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
readonly class Key
{
    public function __construct(public string $key) {}
}