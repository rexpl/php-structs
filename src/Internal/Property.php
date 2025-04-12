<?php

declare(strict_types=1);

namespace Rexpl\Struct\Internal;

use Rexpl\Struct\Contracts\Source;
use Rexpl\Struct\Options;

final readonly class Property
{
    public function __construct(
        public string $name,
        public string $key,
        public bool $hasDefaultValue,
        public Validator $validator,
        public Transformation $transformation,
        public ?string $targetTransformation,
    ) {}

    public function makeProperty(Source $source, Options $options): mixed
    {
        $value = $source->get($this->key);

        // The developer requested validation we run the value through his validator.
        if ($options->validate) {
            $this->validator->validate($source, $this->key);
        }

        // Some kind of transformation is required before initializing this property
        if ($this->transformation !== Transformation::None) {
            // The developer requested as structured array we build using the same options.
            if ($this->transformation === Transformation::ArrayStruct) {
                foreach ($value as &$element) {
                    $element = new ($this->targetTransformation)($element, $options);
                }
            }
            // The type of the child is a struct so we build it as wel
            elseif ($this->transformation === Transformation::ChildStruct) {
                $value = new ($this->targetTransformation)($value, $options);
            }
        }

        return $value;
    }
}