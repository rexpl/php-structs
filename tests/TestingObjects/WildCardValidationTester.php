<?php

declare(strict_types=1);

namespace Tests\TestingObjects;

use Rexpl\Struct\Contracts\Rule;
use Rexpl\Struct\Contracts\Source;

class WildCardValidationTester implements Rule
{
    public static Rule $rule;

    public function validate(Source $source, string $key): bool
    {
        return static::$rule->validate($source, $key);
    }
}