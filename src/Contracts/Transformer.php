<?php

declare(strict_types=1);

namespace Rexpl\Struct\Contracts;

interface Transformer
{
    public function transform(mixed $value): mixed;
}