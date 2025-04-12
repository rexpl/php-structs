<?php

use Rexpl\Struct\Options;
use Rexpl\Struct\Struct;
use Rexpl\Struct\Validate;
use Rexpl\Struct\Validation\IsArray;
use Rexpl\Struct\Validation\IsBoolean;
use Rexpl\Struct\Validation\IsFloat;
use Rexpl\Struct\Validation\IsInteger;
use Rexpl\Struct\Validation\IsString;
use Tests\TestingObjects\ValidationTestCase;
use Tests\TestingObjects\WildCardValidationTester;

test('validation rule: :dataset', function (ValidationTestCase $case) {
        $struct = fn () => new class(['value' => $case->value], new Options(validate: true)) extends Struct {
            #[Validate(new WildCardValidationTester())]
            public mixed $value;
        };

        WildCardValidationTester::$rule = $case->rule;

        if ($case->fail) {
            expect($struct)->toThrow(\Rexpl\Struct\Exceptions\ValidationException::class);
        } else {
            expect($struct())->toBeInstanceOf(Struct::class);
        }
    })
    ->with([
        'is integer: valid' => fn () => new ValidationTestCase(new IsInteger(), false, 50),
        'is integer: invalid' => fn () => new ValidationTestCase(new IsInteger(), true, '50'),
        'is integer (value): too low' => fn () => new ValidationTestCase(new IsInteger(minValue: 1), true, 0),
        'is integer (value): too high' => fn () => new ValidationTestCase(new IsInteger(maxValue: 100), true, 101),
        'is integer (value): in range' => fn () => new ValidationTestCase(new IsInteger(minValue: 10, maxValue: 20), false, 15),
        'is integer (value): in range (min)' => fn () => new ValidationTestCase(new IsInteger(minValue: 1), false, 1),
        'is integer (value): in range (max)' => fn () => new ValidationTestCase(new IsInteger(maxValue: 100), false, 100),

        'is string: valid' => fn () => new ValidationTestCase(new IsString(), false, 'string'),
        'is string: invalid' => fn () => new ValidationTestCase(new IsString(), true, 50),
        'is string (size): too low' => fn () => new ValidationTestCase(new IsString(minSize: 1), true, ''),
        'is string (size): too high' => fn () => new ValidationTestCase(new IsString(maxSize: 4), true, 'hello'),
        'is string (size): in range' => fn () => new ValidationTestCase(new IsString(minSize: 2, maxSize: 5), false, 'abcd'),
        'is string (size): in range (min)' => fn () => new ValidationTestCase(new IsString(minSize: 3), false, 'abc'),
        'is string (size): in range (max)' => fn () => new ValidationTestCase(new IsString(maxSize: 5), false, 'abc'),

        'is float: valid' => fn () => new ValidationTestCase(new IsFloat(), false, 1.23),
        'is float: invalid' => fn () => new ValidationTestCase(new IsFloat(), true, '1.23'),
        'is float (value): too low' => fn () => new ValidationTestCase(new IsFloat(minValue: 1.5), true, 1.4),
        'is float (value): too high' => fn () => new ValidationTestCase(new IsFloat(maxValue: 2.5), true, 2.6),
        'is float (value): in range' => fn () => new ValidationTestCase(new IsFloat(minValue: 1.5, maxValue: 2.5), false, 2.0),
        'is float (value): in range (min)' => fn () => new ValidationTestCase(new IsFloat(minValue: 1.5), false, 1.5),
        'is float (value): in range (max)' => fn () => new ValidationTestCase(new IsFloat(maxValue: 2.5), false, 2.5),

        'is array: valid' => fn () => new ValidationTestCase(new IsArray(), false, ['a', 'b']),
        'is array: invalid' => fn () => new ValidationTestCase(new IsArray(), true, 'not-an-array'),
        'is array (size): too small' => fn () => new ValidationTestCase(new IsArray(minSize: 2), true, ['a']),
        'is array (size): too big' => fn () => new ValidationTestCase(new IsArray(maxSize: 1), true, ['a', 'b']),
        'is array (size): in range' => fn () => new ValidationTestCase(new IsArray(minSize: 1, maxSize: 3), false, ['a', 'b']),
        'is array (size): in range (min)' => fn () => new ValidationTestCase(new IsArray(minSize: 2), false, ['a', 'b']),
        'is array (size): in range (max)' => fn () => new ValidationTestCase(new IsArray(maxSize: 3), false, ['a']),

        'is bool: valid true' => fn () => new ValidationTestCase(new IsBoolean(), false, true),
        'is bool: valid false' => fn () => new ValidationTestCase(new IsBoolean(), false, false),
        'is bool: invalid' => fn () => new ValidationTestCase(new IsBoolean(), true, 1),
    ]);

it('skips next validations with nullable rule with value=null', function () {
    ob_start();
    $struct = new class (['data' => null], new Options(validate: true)) extends Struct {
        #[Validate(
            new \Rexpl\Struct\Validation\Nullable(),
            new \Tests\TestingObjects\OutputClassNameWhenRuleRuns(),
        )]
        public $data;
    };
    $output = ob_get_clean();

    expect($output)->toBe('');
});

it('doesn\'t skip next validations with nullable rule with value!=null', function () {
    ob_start();
    $struct = new class (['data' => 'test'], new Options(validate: true)) extends Struct {
        #[Validate(
            new \Rexpl\Struct\Validation\Nullable(),
            new \Tests\TestingObjects\OutputClassNameWhenRuleRuns(),
        )]
        public $data;
    };
    $output = ob_get_clean();

    expect($output)->toBe(\Tests\TestingObjects\OutputClassNameWhenRuleRuns::class);
});