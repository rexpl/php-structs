<?php

declare(strict_types=1);

use Rexpl\Struct\Struct;
use Tests\Fixtures\Avatar;

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

it('instantiates a child struct correctly', function () {
    $data = [
        'avatar' => [
            'id' => 42,
            'path' => __DIR__,
        ],
    ];

    $user = new class($data) extends Struct {
        public Avatar $avatar;
    };

    expect($user->avatar)
        ->toBeInstanceOf(Avatar::class)
        ->and($user->avatar->id)
        ->toBe(42)
        ->and($user->avatar->path)
        ->toBe(__DIR__);
});

it('instantiates a nullable child struct correctly with value=null', function () {
    $data = [
        'avatar' => null,
    ];

    $user = new class($data) extends Struct {
        public ?Avatar $avatar;
    };

    expect($user->avatar)->toBeNull();
});

it('fails to instantiate a child struct with incorrect data type', function () {
    $data = [
        'avatar' => 'string',
    ];

    expect(fn () => new class($data) extends Struct {
        public Avatar $avatar;
    })->toThrow(\Rexpl\Struct\Exceptions\InvalidStructException::class);
});

it('instantiates a child struct array correctly', function () {
    $data = [
        'avatars' => [
            [
                'id' => 42,
                'path' => __DIR__,
            ],
            (object) [
                'id' => 43,
                'path' => __FILE__,
            ],
        ],
    ];

    $user = new class($data) extends Struct {
        #[\Rexpl\Struct\AsArray(Avatar::class)]
        public array $avatars;
    };

    expect($user->avatars)->toBeArray();

    $first = $user->avatars[0] ?? null;

    expect($first)
        ->toBeInstanceOf(Avatar::class)
        ->and($first->id)
        ->toBe(42)
        ->and($first->path)
        ->toBe(__DIR__);

    $second = $user->avatars[1] ?? null;

    expect($second)
        ->toBeInstanceOf(Avatar::class)
        ->and($second->id)
        ->toBe(43)
        ->and($second->path)
        ->toBe(__FILE__);
});

it('instantiates a nullable child struct array correctly with value=null', function () {
    $data = [
        'avatars' => null,
    ];

    $user = new class($data) extends Struct {
        #[\Rexpl\Struct\AsArray(Avatar::class)]
        public ?array $avatars;
    };

    expect($user->avatars)->toBeNull();
});

it('instantiates a child struct array correctly with invalid data type', function () {
    $data = [
        'avatars' => 42,
    ];

    expect(fn () => new class($data) extends Struct {
        #[\Rexpl\Struct\AsArray(Avatar::class)]
        public array $avatars;
    })->toThrow(\Rexpl\Struct\Exceptions\InvalidStructException::class);
});

it('fails with ambiguous typing on struct array', function () {
    $data = [
        'avatars' => [],
    ];

    expect(fn () => new class($data) extends Struct {
        #[\Rexpl\Struct\AsArray(Avatar::class)]
        public string $avatars;
    })->toThrow(\Rexpl\Struct\Exceptions\AmbiguousTypingException::class);
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