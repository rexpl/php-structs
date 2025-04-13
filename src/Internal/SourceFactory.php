<?php

declare(strict_types=1);

namespace Rexpl\Struct\Internal;

use Rexpl\Struct\Contracts\Source;

class SourceFactory
{
    public static function create(array|object $data): Source
    {
        if (\is_array($data)) {
            return new ArraySource($data);
        } elseif ($data instanceof Source) {
            return $data;
        } else {
            return new ObjectSource($data);
        }
    }
}