<?php

declare(strict_types=1);

namespace Rexpl\Struct\Internal;

use Rexpl\Struct\Contracts\Source;

final readonly class Validator
{
    /**
     * @param \Rexpl\Struct\Contracts\Rule[] $rules
     */
    public function __construct(private array $rules) {}

    public function validate(Source $source, string $key): void
    {
        foreach ($this->rules as $rule) {
            if (!$rule->validate($source, $key)) {
                return;
            }
        }
    }
}