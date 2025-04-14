<?php

declare(strict_types=1);

namespace Rexpl\Struct;

use Rexpl\Struct\Internal\StructManager;
use Rexpl\Struct\Internal\StructInternalState;

final readonly class StructState
{
    public function __construct(
        private StructInternalState $state,
        private StructManager $structManager,
        private Struct $struct,
    ) {}

    public function isValidated(): bool
    {
        return $this->state->validated;
    }

    public function validate(): void
    {
        $this->structManager->validateStruct($this->struct);
        $this->state->validated = true;
    }
}