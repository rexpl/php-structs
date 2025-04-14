<?php

declare(strict_types=1);

namespace Rexpl\Struct\Internal;

use Rexpl\Struct\Contracts\Source;
use Rexpl\Struct\Options;
use Rexpl\Struct\Struct;

final readonly class StructManager
{
    /** @var class-string<\Rexpl\Struct\Struct> */
    public string $struct;

    /** @var \Rexpl\Struct\Internal\PropertyManager[] */
    private array $properties;

    public function __construct(\ReflectionClass $struct)
    {
        $this->struct = $struct->getName();

        $propertyManagers = [];
        $properties = $struct->getProperties();

        foreach ($properties as $property) {
            if (!$property->isStatic() && !$property->isPrivate() && !$property->isReadOnly()) {
                $propertyManagers[] = new PropertyManager($property);
            }
        }

        $this->properties = $propertyManagers;
    }

    public function getProperties(Source $source, Options $options): array
    {
        $properties = [];

        foreach ($this->properties as $property) {
            if ($options->validate) {
                $property->initValidation($source);
            }
            if ($property->shouldMakeProperty($source, $options)) {
                $properties[] = [
                    $property->name, $property->getPropertyValue($source, $options, $this->struct)
                ];
            }
        }

        return $properties;
    }

    public function validateStruct(Struct $struct): void
    {
        $source = SourceFactory::create($struct);

        foreach ($this->properties as $property) {
            $property->deferredValidation($source);
        }
    }
}