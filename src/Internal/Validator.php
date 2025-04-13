<?php

declare(strict_types=1);

namespace Rexpl\Struct\Internal;

use Rexpl\Struct\Contracts\Rule;
use Rexpl\Struct\Contracts\Source;

final class Validator
{
    /** @var \Rexpl\Struct\Contracts\Rule[] */
    private array $rules = [];

    public function validate(Source $source, string $key): void
    {
        $exists = $source->has($key);

        foreach ($this->rules as $rule) {
            if (!$exists && $rule->onlyRunWhenKeySet()) {
                continue;
            } elseif (!$rule->validate($source, $key)) {
                return;
            }
        }
    }

    public function addRules(Rule ...$rules): void
    {
        \array_push($this->rules, ...$rules);
    }
}