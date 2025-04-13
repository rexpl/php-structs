<?php

declare(strict_types=1);

namespace Rexpl\Struct;

use Rexpl\Struct\Internal\SourceFactory;
use Rexpl\Struct\Internal\StructBuilder;

abstract class Struct
{
    /** @var \Rexpl\Struct\Internal\StructBuilder[] */
    private static array $builders = [];

    public function __construct(array|object $data, Options $options = new Options()) {
        if (!\array_key_exists(static::class, self::$builders)) {
            self::$builders[static::class] = new StructBuilder(new \ReflectionClass($this));
        }

        $builder = self::$builders[static::class];
        $source = SourceFactory::create($data);

        foreach ($builder->getProperties($source, $options) as [$name, $value]) {
            $this->{$name} = $value;
        }
    }
}