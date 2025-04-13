<?php

declare(strict_types=1);

namespace Rexpl\Struct\Internal;

use Rexpl\Struct\Contracts\Source;
use Rexpl\Struct\Options;

readonly class StructBuilder
{
    /** @var class-string<\Rexpl\Struct\Struct> */
    public string $struct;

    /** @var \Rexpl\Struct\Internal\PropertyBuilder[] */
    private array $properties;

    public function __construct(\ReflectionClass $struct)
    {
        $this->struct = $struct->getName();

        $builders = [];
        $properties = $struct->getProperties();

        foreach ($properties as $property) {
            if (!$property->isStatic() && !$property->isPrivate() && !$property->isReadOnly()) {
                $builders[] = new PropertyBuilder($property);
            }
        }

        $this->properties = $builders;
    }

    public function getProperties(Source $source, Options $options): array
    {
        $properties = [];

        foreach ($this->properties as $property) {
            if ($options->validate) {
                $property->validate($source);
            }
            if ($property->shouldMakeProperty($source, $options)) {
                $properties[] = [
                    $property->name, $property->getPropertyValue($source, $options, $this->struct)
                ];
            }
        }

        return $properties;
    }
}