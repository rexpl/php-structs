<?php

declare(strict_types=1);

namespace Rexpl\Struct\Internal;

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
}