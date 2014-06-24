<?php

namespace Pagekit\System\Link;

use Pagekit\Framework\ApplicationTrait;

abstract class Link implements LinkInterface, \ArrayAccess
{
    use ApplicationTrait;
}
