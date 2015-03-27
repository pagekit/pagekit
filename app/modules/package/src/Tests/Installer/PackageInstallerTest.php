<?php

namespace Pagekit\Package\Tests;

use Pagekit\Package\Installer\PackageInstaller;
use Pagekit\Package\Loader\JsonLoader;
use Pagekit\Package\Repository\InstalledRepository;

/**
 * Test class for PackageInstaller.
 */
class PackageInstallerTest extends \PHPUnit_Framework_TestCase
{
    use \Pagekit\Tests\FileUtil;

    private $workspace;
    private $installer;
    private $repository;
    private $package;

    public function setUp()
    {
        $this->workspace = $this->getTempDir('package_installer_');

        $loader = new JsonLoader;

        if (!$this->package = $loader->load(__DIR__.'/../Fixtures/Package/extension.json')) {
            $this->markTestSkipped('Unable to load package.');
        }

        $this->repository = new InstalledRepository($this->workspace);
        $this->installer  = new PackageInstaller($this->repository, $loader);
    }

    public function tearDown()
    {
        $this->removeDir($this->workspace);
    }

    public function testInstall()
    {
        $this->installer->install(__DIR__.'/../Fixtures/Package/extension.json');

        $this->assertTrue($this->repository->hasPackage($this->package));
        $this->assertTrue(file_exists($this->workspace.'/test'));
        $this->assertTrue(file_exists($this->workspace.'/test/extension.json'));
        $this->assertTrue(file_exists($this->workspace.'/test/directory/test'));
    }

    public function testIsInstalled()
    {
        $this->assertFalse($this->installer->isInstalled($this->package));
        $this->installer->install(__DIR__.'/../Fixtures/Package/extension.json');
        $this->assertTrue($this->installer->isInstalled($this->package));
    }

    public function testUpdate()
    {
        $this->installer->install(__DIR__.'/../Fixtures/Package/extension.json');

        $this->assertEquals('0.0.1', $this->repository->findPackage('test')->getVersion());
        $this->installer->update(__DIR__.'/../Fixtures/Package2/extension.json');
        $this->assertEquals('0.0.2', $this->repository->findPackage('test')->getVersion());
    }

    public function testUninstall()
    {
        $this->installer->install(__DIR__.'/../Fixtures/Package/extension.json');

        $this->assertTrue($this->repository->hasPackage($this->package));
        $this->installer->uninstall($this->package);
        $this->assertFalse($this->repository->hasPackage($this->package));
        $this->assertFalse(file_exists($this->workspace.'/test'));
    }
}
