<?php

declare(strict_types=1);

namespace Rexpl\Struct\Validation;

use Rexpl\Struct\Contracts\Rule;
use Rexpl\Struct\Contracts\Source;

class Nullable implements Rule
{
    public function validate(Source $source, string $key): bool
    {
        return $source->get($key) !== null;
    }
}