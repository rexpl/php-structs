<?php

declare(strict_types=1);

namespace Rexpl\Struct;

use Rexpl\Struct\Internal\SourceFactory;
use Rexpl\Struct\Internal\StructManager;
use Rexpl\Struct\Internal\StructInternalState;

abstract class Struct
{
    /** @var \Rexpl\Struct\Internal\StructManager[] */
    private static array $managers = [];

    private StructInternalState $structState__986cdf0686453a888;

    public function __construct(array|object $data, Options $options = new Options()) {
        if (!\array_key_exists(static::class, self::$managers)) {
            self::$managers[static::class] = new StructManager(new \ReflectionClass($this));
        }

        $builder = self::$managers[static::class];
        $source = SourceFactory::create($data);

        foreach ($builder->getProperties($source, $options) as [$name, $value]) {
            $this->{$name} = $value;
        }

        $this->structState__986cdf0686453a888 = new StructInternalState($options->validate);
    }

    public function struct(): StructState
    {
        return new StructState($this->structState__986cdf0686453a888, self::$managers[static::class], $this);
    }
}