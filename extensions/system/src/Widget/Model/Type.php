<?php

namespace Pagekit\Widget\Model;

use Pagekit\Framework\ApplicationTrait;

abstract class Type implements TypeInterface, \ArrayAccess
{
    use ApplicationTrait;
}
