<?php

declare(strict_types=1);

namespace Rexpl\Struct\Internal;

readonly class AnonymousBackedEnum
{
    public bool $isStringBacked;
    public bool $isBackedEnum;

    /**
     * @param class-string<\BackedEnum> $enum
     */
    public function __construct(public string $enum)
    {
        if (!\is_subclass_of($enum, \UnitEnum::class)) {
            throw new \InvalidArgumentException(\sprintf(
                'Enum must be an enum, value of type %s given,', \gettype($enum)
            ));
        }

        $backingType = new \ReflectionEnum($this->enum)->getBackingType()?->getName();
        $this->isBackedEnum = $backingType !== null;
        $this->isStringBacked = $backingType === 'string';
    }

    public function is(mixed $value): bool
    {
        return $value instanceof $this->enum;
    }

    public function try(mixed $value): ?\BackedEnum
    {
        // avoid a type error
        $try = $this->isStringBacked ? \is_string($value) : \is_int($value);

        if ($try) {
            return [$this->enum, 'tryFrom']($value);
        } else {
            return null;
        }
    }
}
