<?php

declare(strict_types=1);

namespace Rexpl\Struct\Exceptions;

class MissingRequiredValueException extends StructBuildException
{
    public function __construct(string $struct, string $key)
    {
        $message = \sprintf('Missing required value "%s" in struct "%s".', $key, $struct);
        parent::__construct($message);
    }
}