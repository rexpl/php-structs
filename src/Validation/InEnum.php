<?php

declare(strict_types=1);

namespace Rexpl\Struct\Validation;

use Rexpl\Struct\Contracts\Rule;
use Rexpl\Struct\Contracts\Source;
use Rexpl\Struct\Exceptions\ValidationException;
use Rexpl\Struct\Internal\AnonymousBackedEnum;

readonly class InEnum implements Rule
{
    public AnonymousBackedEnum $enum;

    /**
     * @param class-string $enum
     */
    public function __construct(string $enum)
    {
        $this->enum = new AnonymousBackedEnum($enum);
    }

    public function validate(Source $source, string $key): bool
    {
        $value = $source->get($key);

        if ($this->enum->is($value)) {
            return true;
        }

        if ($this->enum->isBackedEnum && $this->enum->try($value)) {
            return true;
        }

        throw new ValidationException(\sprintf(
            '%s should be a valid %s enum case.', $key, $this->enum->enum
        ));
    }

    public function onlyRunWhenKeySet(): bool
    {
        return true;
    }
}
