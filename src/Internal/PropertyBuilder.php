<?php

declare(strict_types=1);

namespace Rexpl\Struct\Internal;

use Rexpl\Struct\AsArray;
use Rexpl\Struct\Contracts\Source;
use Rexpl\Struct\Exceptions\AmbiguousTypingException;
use Rexpl\Struct\Exceptions\InvalidStructException;
use Rexpl\Struct\Exceptions\MissingRequiredValueException;
use Rexpl\Struct\Key;
use Rexpl\Struct\Options;
use Rexpl\Struct\Struct;
use Rexpl\Struct\Validate;

final class PropertyBuilder
{
    public readonly string $name;
    private string $key;
    private readonly bool $hasDefaultValue;
    private Validator $validator;
    private Transformation $transformation = Transformation::None;
    private ?string $targetTransformation = null;

    public function __construct(\ReflectionProperty $property)
    {
        $this->name = $this->key = $property->getName();
        $this->hasDefaultValue = $property->hasDefaultValue();
        $this->validator = new Validator();

        $this->analyseAttributes($property);
        $this->analyseTyping($property);
    }

    private function analyseAttributes(\ReflectionProperty $property): void
    {
        $attributes = $property->getAttributes();

        foreach ($attributes as $attribute) {
            switch ($attribute->getName()) {
                case Key::class:
                    /** @var \Rexpl\Struct\Key $suppliedKey */
                    $suppliedKey = $attribute->newInstance();
                    $this->key = $suppliedKey->key;
                    break;
                case Validate::class:
                    /** @var \Rexpl\Struct\Validate $suppliedValidator */
                    $suppliedValidator = $attribute->newInstance();
                    $this->validator->addRules(...$suppliedValidator->rules);
                    break;
                case AsArray::class:
                    /** @var \Rexpl\Struct\AsArray $asArray */
                    $asArray = $attribute->newInstance();
                    $this->transformation = Transformation::ArrayStruct;
                    $this->targetTransformation = $asArray->target;
                    break;
            }
        }
    }

    private function analyseTyping(\ReflectionProperty $property): void
    {
        $type = $property->getType();

        if (!$type instanceof \ReflectionNamedType) {
            return;
        }

        if ($this->transformation === Transformation::ArrayStruct && $type->getName() !== 'array') {
            throw new AmbiguousTypingException(\sprintf(
                'Requested array struct for property "%s" without type array.', $this->name
            ));
        }

        $potentialStruct = $type->getName();
        if (
            \class_exists($potentialStruct)
            && \in_array(Struct::class, \class_parents($potentialStruct), true)
        ) {
            $this->transformation = Transformation::ChildStruct;
            $this->targetTransformation = $type->getName();
        }
    }

    public function validate(Source $source): void
    {
        $this->validator->validate($source, $this->key);
    }

    public function shouldMakeProperty(Source $source, Options $options): bool
    {
        return $source->has($this->key)
            || $options->requireAllProperties
            || !$this->hasDefaultValue && $options->requireAllPropertiesWithoutDefaultValue;
    }

    public function getPropertyValue(Source $source, Options $options, string $struct): mixed
    {
        if (!$source->has($this->key)) {
            throw new MissingRequiredValueException(\sprintf(
                'Missing required value "%s" in struct "%s".', $this->key, $struct
            ));
        }

        $value = $source->get($this->key);

        switch ($this->transformation) {
            case Transformation::None:
                return $value;

            case Transformation::ArrayStruct:
                return $this->createArrayOfStructs($value, $options);

            case Transformation::ChildStruct:
                return $this->createChildStruct($value, $options, true);
        }

        throw new \LogicException();
    }

    private function createArrayOfStructs(mixed $values, Options $options): ?array
    {
        if ($values === null) {
            // If this property is not nullable we let php throw a type error.
            return null;
        } elseif (!\is_array($values)) {
            throw new InvalidStructException(\sprintf(
                'Cannot create child struct array %s from %s.', $this->targetTransformation, \gettype($values)
            ));
        }

        return \array_map(fn ($v) => $this->createChildStruct($v, $options, false), $values);
    }

    private function createChildStruct(mixed $value, Options $options, bool $allowNull): ?Struct
    {
        if ($allowNull && $value === null) {
            // If this property is not nullable we let php throw a type error.
            return null;
        } elseif (\is_array($value) || \is_object($value)) {
            return new ($this->targetTransformation)($value, $options);
        } else {
            throw new InvalidStructException(\sprintf(
                'Cannot create child struct %s from %s.', $this->targetTransformation, \gettype($value)
            ));
        }
    }
}