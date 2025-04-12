<?php

declare(strict_types=1);

namespace Rexpl\Struct;

final readonly class Options
{
    public function __construct(
        public bool $validate = false,
        public bool $requireAllPropertiesWithoutDefaultValue = true,
        public bool $requireAllProperties = false,
    ) {}
}