<?php

declare(strict_types=1);

namespace Rexpl\Struct\Internal;

enum Transformation
{
    case None;
    case ChildStruct;
    case ArrayStruct;
}