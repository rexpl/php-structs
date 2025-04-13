<?php

declare(strict_types=1);

namespace Rexpl\Struct;

use Rexpl\Struct\Internal\StructBuilder;
use Rexpl\Struct\Internal\StructInternalState;

final readonly class StructState
{
    public function __construct(
        private StructInternalState $state,
        private StructBuilder $structBuilder,
        private Struct $struct,
    ) {}

    public function isValidated(): bool
    {
        return $this->state->validated;
    }

    public function validate(): void
    {
        $this->structBuilder->validateStruct($this->struct);
        $this->state->validated = true;
    }
}