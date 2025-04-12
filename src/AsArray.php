<?php

declare(strict_types=1);

namespace Rexpl\Struct;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
readonly class AsArray
{
    /**
     * @param class-string<\Rexpl\Struct\Struct> $target
     */
    public function __construct(public string $target) {}
}