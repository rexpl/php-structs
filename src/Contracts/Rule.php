<?php

declare(strict_types=1);

namespace Rexpl\Struct\Contracts;

interface Rule
{
    /**
     * @param \Rexpl\Struct\Contracts\Source $source
     * @param string $key The source key of the property we are validating.
     * @return bool Returns true to continue validating, return false to stop validating.
     * @throws \Rexpl\Struct\Exceptions\ValidationException if the validation condition is not met.
     */
    public function validate(Source $source, string $key): bool;

    /**
     * @return bool Returns true if the rule should only run when the property exists.
     */
    public function onlyRunWhenKeySet(): bool;
}