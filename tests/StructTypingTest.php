<?php

declare(strict_types=1);

use Rexpl\Struct\Struct;
use Tests\Fixtures\Avatar;
use Tests\Fixtures\TestingBackedEnum;

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

it('creates an enum case correctly', function () {
    $data = [
        'value' => 'case_1',
    ];

    $struct = new class($data) extends Struct {
        public TestingBackedEnum $value;
    };

    expect($struct->value)
        ->toBeInstanceOf(TestingBackedEnum::class)
        ->toBe(TestingBackedEnum::Case1);
});

it('creates an enum case from an already valid case', function () {
    $data = [
        'value' => TestingBackedEnum::Case2,
    ];

    $struct = new class($data) extends Struct {
        public TestingBackedEnum $value;
    };

    expect($struct->value)
        ->toBeInstanceOf(TestingBackedEnum::class)
        ->toBe(TestingBackedEnum::Case2);
});

it('allows a nullable enum case to be null', function () {
    $data = [
        'value' => null,
    ];

    $struct = new class($data) extends Struct {
        public ?TestingBackedEnum $value;
    };

    expect($struct->value)->toBeNull();
});

it('fails with an invalid enum case', function () {
    $data = [
        'value' => 'case_3',
    ];

    expect(fn () => new class($data) extends Struct {
        public TestingBackedEnum $value;
    })->toThrow(\Rexpl\Struct\Exceptions\InvalidStructException::class);
});