<?php

declare(strict_types=1);

namespace Rexpl\Struct\Internal;

final class StructInternalState
{
    public function __construct(public bool $validated) {}
}