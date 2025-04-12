<?php

declare(strict_types=1);

namespace Rexpl\Struct\Validation;

use Rexpl\Struct\Contracts\Rule;
use Rexpl\Struct\Contracts\Source;
use Rexpl\Struct\Exceptions\ValidationException;

readonly class IsFloat implements Rule
{
    public function __construct(public ?float $minValue = null, public ?float $maxValue = null) {}

    public function validate(Source $source, string $key): bool
    {
        $value = $source->get($key);
        if (!\is_float($value)) {
            throw new ValidationException(\sprintf('%s should be a valid float.', $key));
        } elseif ($this->minValue !== null && $value < $this->minValue) {
            throw new ValidationException(\sprintf('%s should be higher than %f.', $key, $this->minValue));
        } elseif ($this->maxValue !== null && $value > $this->maxValue) {
            throw new ValidationException(\sprintf('%s should not be higher than %f.', $key, $this->maxValue));
        }

        return true;
    }
}