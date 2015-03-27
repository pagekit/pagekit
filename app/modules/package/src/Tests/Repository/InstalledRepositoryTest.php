<?php

namespace Pagekit\Package\Tests\Repository;

use Pagekit\Package\Repository\InstalledRepository;

class InstalledRepositoryTest extends RepositoryTest
{
    public function testGetInstallPath()
    {
        $package = new \Pagekit\Package\Package('test', '0.0.1', '0.0.1');
        $this->assertEquals('/testpath/test', $this->getRepository()->getInstallPath($package));
    }

    protected function getRepository()
    {
        return new InstalledRepository('/testpath');
    }
}
