<?php

declare(strict_types=1);

namespace Tests\TestingObjects;

use Rexpl\Struct\Struct;

class Avatar extends Struct
{
    public int $id;
    public string $path;
}