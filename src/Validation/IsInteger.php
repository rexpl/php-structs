<?php

declare(strict_types=1);

namespace Rexpl\Struct\Validation;

use Rexpl\Struct\Contracts\Rule;
use Rexpl\Struct\Contracts\Source;
use Rexpl\Struct\Exceptions\ValidationException;

readonly class IsInteger implements Rule
{
    public function __construct(public ?int $minValue = null, public ?int $maxValue = null) {}

    public function validate(Source $source, string $key): bool
    {
        $value = $source->get($key);
        if (!\is_int($value)) {
            throw new ValidationException(\sprintf('%s should be a valid integer.', $key));
        } elseif ($this->minValue !== null && $value < $this->minValue) {
            throw new ValidationException(\sprintf('%s should be higher than %d.', $key, $this->minValue));
        } elseif ($this->maxValue !== null && $value > $this->maxValue) {
            throw new ValidationException(\sprintf('%s should not be higher than %d.', $key, $this->maxValue));
        }

        return true;
    }

    public function onlyRunWhenKeySet(): bool
    {
        return true;
    }
}