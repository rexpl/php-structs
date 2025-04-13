<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Rexpl\Struct\Contracts\Rule;
use Rexpl\Struct\Contracts\Source;

class OutputClassNameWhenRuleRuns implements Rule
{
    public function validate(Source $source, string $key): bool
    {
        echo static::class;
        return true;
    }

    public function onlyRunWhenKeySet(): bool
    {
        return true;
    }
}