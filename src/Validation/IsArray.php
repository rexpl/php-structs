<?php

declare(strict_types=1);

namespace Rexpl\Struct\Validation;

use Rexpl\Struct\Contracts\Rule;
use Rexpl\Struct\Contracts\Source;
use Rexpl\Struct\Exceptions\ValidationException;

readonly class IsArray implements Rule
{
    public function __construct(public ?int $minSize = null, public ?int $maxSize = null) {}

    public function validate(Source $source, string $key): bool
    {
        $value = $source->get($key);
        if (!\is_array($value)) {
            throw new ValidationException(\sprintf('%s should be a valid array.', $key));
        }

        $count = \count($value);
        if ($this->minSize !== null && $count < $this->minSize) {
            throw new ValidationException(\sprintf('%s should contain at least %d items.', $key, $this->minSize));
        } elseif ($this->maxSize !== null && $count > $this->maxSize) {
            throw new ValidationException(\sprintf('%s should not contain more than %d items.', $key, $this->maxSize));
        }

        return true;
    }
}