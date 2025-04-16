<?php

declare(strict_types=1);

use Rexpl\Struct\Struct;

it('instantiates a simple struct with one property from array', function () {
    $data = ['test' => 'hello'];
    $struct = new class($data) extends Struct {
        public string $test;
    };

    expect($struct->test)->toBe('hello');
});

it('instantiates a simple struct with one property from \stdClass', function () {
    $data = new \stdClass();
    $data->test = 'hello';

    $struct = new class($data) extends Struct {
        public string $test;
    };

    expect($struct->test)->toBe('hello');
});

it('instantiates a simple struct with one property from object', function () {
    $data = new class() {
        public string $test = 'hello';
    };

    $struct = new class($data) extends Struct {
        public string $test;
    };

    expect($struct->test)->toBe('hello');
});

it('maps key names using the key attribute correctly', function () {
    $data = [
        'actual_key' => 'mapped value',
    ];

    $struct = new class($data) extends Struct {
        #[\Rexpl\Struct\Key('actual_key')]
        public string $alias;
    };

    expect($struct->alias)->toBe('mapped value');
});

it('fails when missing a property without default value', function () {
    $data = [
        'id' => 42,
    ];

    expect(fn () => new class($data) extends Struct {
        public int $id;
        public string $name;
    })->toThrow(\Rexpl\Struct\Exceptions\MissingRequiredValueException::class);
});

it('doesn\'t fail when a missing property has a default value', function () {
    $data = [
        'id' => 42,
    ];

    $struct = new class($data) extends Struct {
        public int $id;
        public string $name = 'John Doe';
    };

    expect($struct->name)->toBe('John Doe');
});

it('ignores private properties', function () {
    $data = [
        'id' => 42,
        'name' => 'Alice',
    ];

    $struct = new class($data) extends Struct {
        public int $id;
        private string $name = 'Bob';

        public function getName(): string
        {
            return $this->name;
        }
    };

    expect($struct->getName())->toBe('Bob');
});

it('ignores readonly properties', function () {
    $data = [
        'id' => 42,
        'name' => 'Alice',
    ];

    $struct = new class($data) extends Struct {
        public int $id;
        public readonly string $name;
    };

    expect(isset($struct->name))->toBe(false);
});