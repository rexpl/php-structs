<?php

declare(strict_types=1);

namespace Rexpl\Struct;

use Rexpl\Struct\Contracts\Rule;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
readonly class Validate
{
    /** @var \Rexpl\Struct\Contracts\Rule[] */
    public array $rules;

    public function __construct(Rule ...$rules)
    {
        $this->rules = $rules;
    }
}