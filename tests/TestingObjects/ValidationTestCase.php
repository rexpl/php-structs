<?php

declare(strict_types=1);

namespace Tests\TestingObjects;

use Rexpl\Struct\Contracts\Rule;

readonly class ValidationTestCase
{
    public function __construct(public Rule $rule, public bool $fail, public mixed $value) {}
}