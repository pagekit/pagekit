<?php

namespace Pagekit\Migration\Tests;

use Pagekit\Migration\Migrator;

/**
 * Test class for Migrations.
 */
class MigrationTest extends \PHPUnit_Framework_TestCase
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

    public function testUp()
    {
        $migration = $this->migrator->create(__DIR__.'/Fixtures');
        $this->assertEquals(
            [
                '0000_00_00_000000_init',
                '0000_00_00_000001_test1',
                '0000_00_00_000002_test2',
                '0000_00_00_000003_test3',
            ],
            $migration->get('0000_00_00_000003_test3', 'up')
        );
        $this->assertEquals('0000_00_00_000003_test3', $migration->run('0000_00_00_000003_test3'));

        $migration = $this->migrator->create(__DIR__.'/Fixtures', '0000_00_00_000003_test3');
        $this->assertEquals(
            [
                '0000_00_00_000004_test4',
                '0000_00_00_000005_test5',
            ],
            $migration->get('0000_00_00_000005_test5', 'up')
        );
        $this->assertEquals('0000_00_00_000005_test5', $migration->run('0000_00_00_000005_test5'));
    }

    public function testDown()
    {
        $migration = $this->migrator->create(__DIR__.'/Fixtures', '0000_00_00_000003_test3');
        $this->assertEquals(
            [
                '0000_00_00_000003_test3',
                '0000_00_00_000002_test2',
                '0000_00_00_000001_test1',
                '0000_00_00_000000_init',
            ],
            $migration->get(null, 'down')
        );
        $this->assertEquals('0000_00_00_000002_test2', $migration->run('0000_00_00_000001_test1'));

        $migration = $this->migrator->create(__DIR__.'/Fixtures', '0000_00_00_000004_test4');
        $this->assertEquals(
            [
                '0000_00_00_000004_test4',
                '0000_00_00_000003_test3',
            ],
            $migration->get('0000_00_00_000002_test2', 'down')
        );
        $this->assertEquals('0000_00_00_000003_test3', $migration->run('0000_00_00_000002_test2'));
    }

    public function testLatest()
    {
        $migration = $this->migrator->create(__DIR__.'/Fixtures');
        $this->assertEquals(
            [
                '0000_00_00_000000_init',
                '0000_00_00_000001_test1',
                '0000_00_00_000002_test2',
                '0000_00_00_000003_test3',
                '0000_00_00_000004_test4',
                '0000_00_00_000005_test5',
            ],
            $migration->get()
        );

        $reflectionObject = new \ReflectionObject($migration);
        $loadMethod = $reflectionObject->getMethod('load');
        $loadMethod->setAccessible(true);
        $files = $loadMethod->invokeArgs($migration, []);
        $this->assertCount(6, $files);

        $migration = $this->migrator->create(__DIR__.'/Fixtures', '0000_00_00_000004_test4');

        $reflectionObject = new \ReflectionObject($migration);
        $loadMethod = $reflectionObject->getMethod('load');
        $loadMethod->setAccessible(true);
        $files = $loadMethod->invokeArgs($migration, ['0000_00_00_000004_test4', null]);
        $this->assertCount(1, $files);
    }
}
