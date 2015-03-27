<?php

namespace Pagekit\Package\Tests\Repository;

use Pagekit\Package\Repository\ArrayRepository;

class ArrayRepositoryTest extends RepositoryTest
{
    protected function getRepository()
    {
        return new ArrayRepository;
    }
}
