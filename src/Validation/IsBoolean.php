<?php

declare(strict_types=1);

namespace Rexpl\Struct\Validation;


use Rexpl\Struct\Contracts\Rule;
use Rexpl\Struct\Contracts\Source;
use Rexpl\Struct\Exceptions\ValidationException;

readonly class IsBoolean implements Rule
{
    public function validate(Source $source, string $key): bool
    {
        $value = $source->get($key);
        if (!\is_bool($value)) {
            throw new ValidationException(\sprintf('%s should be a boolean.', $key));
        }

        return true;
    }

    public function onlyRunWhenKeySet(): bool
    {
        return true;
    }
}