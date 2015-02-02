<?php

namespace Pagekit\Migration\Tests;

use Pagekit\Migration\Migrator;

/**
 * Test class for Migrations.
 */
class MigratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Migrator
     */
    protected $migrator;

    public function setUp()
    {
        $this->migrator = new Migrator();
        $this->migrator->addGlobal('app', new \StdClass());
    }

	public function testCreatesMigration()
    {
        $migration = $this->migrator->create(__DIR__.'/Fixtures');
		$this->assertInstanceOf('Pagekit\Migration\Migration', $migration);
	}

	public function testCreateException()
    {
		$this->setExpectedException('InvalidArgumentException');
        $this->migrator->create(__DIR__.'/invalidPath');
	}

	public function testGetGlobals()
    {
        $this->migrator->addGlobal('app', new \stdClass());
        $this->assertArrayHasKey('app', $this->migrator->getGlobals());
	}
}
