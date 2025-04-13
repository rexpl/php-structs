<?php

declare(strict_types=1);

namespace Rexpl\Struct\Validation;

use Rexpl\Struct\Contracts\Rule;
use Rexpl\Struct\Contracts\Source;
use Rexpl\Struct\Exceptions\ValidationException;

readonly class IsString implements Rule
{
    public function __construct(public ?int $minSize = null, public ?int $maxSize = null) {}

    public function validate(Source $source, string $key): bool
    {
        $value = $source->get($key);
        if (!\is_string($value)) {
            throw new ValidationException(\sprintf('"%s" should be a string.', $key));
        } elseif ($this->minSize !== null && \strlen($value) < $this->minSize) {
            throw new ValidationException(\sprintf('"%s" should be at least %d bytes.', $key, $this->minSize));
        } elseif ($this->maxSize !== null && \strlen($value) > $this->maxSize) {
            throw new ValidationException(\sprintf('"%s" should not be larger %d bytes.', $key, $this->minSize));
        }

        return true;
    }

    public function onlyRunWhenKeySet(): bool
    {
        return true;
    }
}