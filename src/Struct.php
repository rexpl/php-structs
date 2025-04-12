<?php

declare(strict_types=1);

namespace Rexpl\Struct;

use Rexpl\Struct\Exceptions\MissingRequiredValueException;
use Rexpl\Struct\Internal\Property;
use Rexpl\Struct\Internal\Transformation;
use Rexpl\Struct\Internal\Validator;
use Rexpl\Struct\Sources\ArraySource;
use Rexpl\Struct\Sources\ObjectSource;

abstract class Struct
{
    /** @var \Rexpl\Struct\Internal\Property[][] */
    private static array $properties = [];

    public function __construct(array|object $data, Options $options = new Options()) {
        if (!\array_key_exists(static::class, self::$properties)) {
            $this->boot();
        }

        $properties = self::$properties[static::class];
        $source = \is_array($data) ? new ArraySource($data) : new ObjectSource($data);;

        foreach ($properties as $property) {
            // The property is present in the source we can work from here.
            if ($source->has($property->key)) {
                $this->{$property->name} = $property->makeProperty($source, $options);
            }
            // the property is not present in the source:
            //  - if the property has a default value and "requireAllProperties" is true we throw an exception
            //  - if the property doesn't have a default value and "requireAllPropertiesWithoutDefaultValue" is true we
            // throw an exception
            //  - the property has a default value we let php do it's magic!
            elseif (
                $property->hasDefaultValue && $options->requireAllProperties
                || !$property->hasDefaultValue && $options->requireAllPropertiesWithoutDefaultValue
            ) {
                throw new MissingRequiredValueException(static::class, $property->key);
            }
        }
    }

    private function boot(): void
    {
        $self = new \ReflectionClass($this);
        $properties = $self->getProperties();

        foreach ($properties as $property) {
            if (!$property->isStatic() && !$property->isPrivate() && !$property->isReadOnly()) {
                self::$properties[static::class][] = $this->initializeProperty($property);
            }
        }
    }

    private function initializeProperty(\ReflectionProperty $property): Property
    {
        $name = $property->getName();
        $key = $property->getName();
        $hasDefaultValue = $property->hasDefaultValue();
        $rules = [];
        $transformation = Transformation::None;
        $targetTransformation = null;

        $type = $property->getType();
        if ($type instanceof \ReflectionNamedType) {
            $potentialStruct = $type->getName();
            if (
                \class_exists($potentialStruct)
                && \in_array(Struct::class, \class_parents($potentialStruct), true)
            ) {
                $transformation = Transformation::ChildStruct;
                $targetTransformation = $type->getName();
            }
        }

        $attributes = $property->getAttributes();

        foreach ($attributes as $attribute) {
            switch ($attribute->getName()) {
                case Key::class:
                    /** @var \Rexpl\Struct\Key $suppliedKey */
                    $suppliedKey = $attribute->newInstance();
                    $key = $suppliedKey->key;
                    break;
                case Validate::class:
                    /** @var \Rexpl\Struct\Validate $suppliedValidator */
                    $suppliedValidator = $attribute->newInstance();
                    \array_push($rules, ...$suppliedValidator->rules);
                    break;
                case AsArray::class:
                    /** @var \Rexpl\Struct\AsArray $asArray */
                    $asArray = $attribute->newInstance();
                    $transformation = Transformation::ArrayStruct;
                    $targetTransformation = $asArray->target;
                    break;
            }
        }

        $validator = new Validator($rules);

        return new Property(
            $name, $key, $hasDefaultValue, $validator, $transformation, $targetTransformation
        );
    }
}