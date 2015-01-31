<?php

namespace Pagekit\Package\Tests\Repository;

use Pagekit\Package\Package;

abstract class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!extension_loaded('curl')) {
            $this->markTestSkipped(sprintf('CURL extension missing.'));
            return;
        }
    }

    public function testAddPackage()
    {
        $repo = $this->getRepository();
        $repo->addPackage(new Package('foo', '1', '1'));

        $this->assertEquals(1, count($repo));
    }

    public function testRemovePackage()
    {
        $package = new Package('bar', '2', '2');

        $repo = $this->getRepository();
        $repo->addPackage(new Package('foo', '1', '1'));
        $repo->addPackage($package);

        $this->assertEquals(2, count($repo));

        $repo->removePackage(new Package('foo', '1', '1'));

        $this->assertEquals(1, count($repo));
        $this->assertEquals([$package], $repo->getPackages());
    }

    public function testHasPackage()
    {
        $repo = $this->getRepository();
        $repo->addPackage(new Package('foo', '1', '1'));
        $repo->addPackage(new Package('bar', '2', '2'));

        $this->assertTrue($repo->hasPackage(new Package('foo', '1', '1')));
        $this->assertFalse($repo->hasPackage(new Package('bar', '1', '1')));
    }

    public function testFindPackages()
    {
        $repo = $this->getRepository();
        $repo->addPackage(new Package('foo', '1', '1'));
        $repo->addPackage(new Package('bar', '2', '2'));
        $repo->addPackage(new Package('bar', '3', '3'));

        $foo = $repo->findPackages('foo');
        $this->assertCount(1, $foo);
        $this->assertEquals('foo', $foo[0]->getName());

        $bar = $repo->findPackages('bar');
        $this->assertCount(2, $bar);
        $this->assertEquals('bar', $bar[0]->getName());
    }

    abstract protected function getRepository();
}
