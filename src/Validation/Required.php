<?php

declare(strict_types=1);

namespace Rexpl\Struct\Validation;

use Rexpl\Struct\Contracts\Rule;
use Rexpl\Struct\Contracts\Source;
use Rexpl\Struct\Exceptions\ValidationException;

class Required implements Rule
{
    public function validate(Source $source, string $key): bool
    {
        if ($source->has($key)) {
            return true;
        }

        throw new ValidationException(\sprintf('"%s" is required.', $key));
    }

    public function onlyRunWhenKeySet(): bool
    {
        return false;
    }
}