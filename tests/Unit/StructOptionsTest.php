<?php

use Rexpl\Struct\Options;
use Rexpl\Struct\Struct;

it('fails with a missing property and requireAllPropertiesWithoutDefaultValue=true', function () {
    $data = [
        'id' => 42,
    ];

    $options = new Options(requireAllPropertiesWithoutDefaultValue: true);

    expect(fn () => new class($data, $options) extends Struct {
        public int $id;
        public string $name;
    })->toThrow(\Rexpl\Struct\Exceptions\MissingRequiredValueException::class);
});

it('doesn\'t fail with a missing property and requireAllPropertiesWithoutDefaultValue=false', function () {
    $data = [
        'id' => 42,
    ];

    $options = new Options(requireAllPropertiesWithoutDefaultValue: false);

    $struct = new class($data, $options) extends Struct {
        public int $id;
        public string $name;
    };

    expect($struct->id)
        ->toBe(42)
        ->and(isset($struct->name))
        ->toBe(false);
});

it('fails with a missing property with a default value and requireAllProperties=true', function () {
    $data = [
        'id' => 42,
    ];

    $options = new Options(requireAllProperties: true);

    expect(fn () => new class($data, $options) extends Struct {
        public int $id;
        public string $name = 'John Doe';
    })->toThrow(\Rexpl\Struct\Exceptions\MissingRequiredValueException::class);
});

it('doesn\'t fail with a missing property with a default value and requireAllProperties=false', function () {
    $data = [
        'id' => 42,
    ];

    $options = new Options(requireAllProperties: false);

    $struct = new class($data, $options) extends Struct {
        public int $id;
        public string $name = 'John Doe';
    };

    expect($struct->id)
        ->toBe(42)
        ->and($struct->name)
        ->toBe('John Doe');
});

it('doesn\'t validate when validate=false', function () {
    $data = [
        'id' => 42,
    ];

    $options = new Options(validate: false);

    ob_start();
    $struct = new class($data, $options) extends Struct {
        #[\Rexpl\Struct\Validate(new \Tests\TestingObjects\OutputClassNameWhenRuleRuns())]
        public int $id;
    };
    $output = ob_get_clean();

    expect($output)->toBe('');
});

it('runs validations when validate=true', function () {
    $data = [
        'id' => 42,
    ];

    $options = new Options(validate: true);

    ob_start();
    $struct = new class($data, $options) extends Struct {
        #[\Rexpl\Struct\Validate(new \Tests\TestingObjects\OutputClassNameWhenRuleRuns())]
        public int $id;
    };
    $output = ob_get_clean();

    expect($output)->toBe(\Tests\TestingObjects\OutputClassNameWhenRuleRuns::class);
});