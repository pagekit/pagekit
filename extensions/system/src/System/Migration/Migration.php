<?php

namespace Pagekit\System\Migration;

use Pagekit\Component\Database\ConnectionWrapper;
use Pagekit\Component\Database\Utility;
use Pagekit\Component\Migration\MigrationInterface;
use Pagekit\Framework\ApplicationTrait;

abstract class Migration implements MigrationInterface, \ArrayAccess {
    use ApplicationTrait;

    /**
     * @return ConnectionWrapper
     */
    public function getConnection()
    {
        return $this['db'];
    }

    /**
     * @return Utility
     */
    public function getUtility()
    {
        return $this->getConnection()->getUtility();
    }
}