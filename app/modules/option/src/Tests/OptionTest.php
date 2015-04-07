<?php

namespace Pagekit\Option\Tests;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Pagekit\Option\Option;

class OptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $connection = $this->getConnection();
        $connection->expects($this->once())
                   ->method('fetchAssoc')
                   ->will($this->returnValue(['value' => json_encode('bar')]));

        $options = $this->getOptions($connection, $cache = $this->getCache());

        // get from database
        $this->assertEquals('bar', $options->get('foo'));

        // check cached value
        $this->assertTrue($cache->contains('Options:foo'));

        // get from cache
        $options = $this->getOptions($connection, $cache);
        $this->assertEquals('bar', $options->get('foo'));
    }

    public function testGetWithAutoload()
    {
        $connection = $this->getConnection();
        $connection->expects($this->once())
                   ->method('fetchAll')
                   ->will($this->returnValue([['name' => 'foo', 'value' => json_encode('bar'), 'autoload' => 1]]));

        $options = $this->getOptions($connection, $cache = $this->getCache());

        // get from database
        $this->assertEquals('bar', $options->get('foo'));

        // check cached value
        $this->assertTrue($cache->contains('Options:Autoload'));

        // get from cache
        $options = $this->getOptions($connection, $cache);
        $this->assertEquals('bar', $options->get('foo'));
    }

    public function testGetIgnored()
    {
        $connection = $this->getConnection();
        $options = $this->getOptions($connection, $cache = $this->getCache());
        $cache->save('Options.Ignore', ['Ignored' => 'value']);

        $this->assertEquals(null, $options->get('Ignored'));

    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetEmptyOptionName()
    {
        $options = $this->getOptions();
        $options->get(null);
    }

    public function testSet()
    {
        $connection = $this->getConnection();
        $connection->expects($this->once())
                   ->method('executeQuery')
                   ->will($this->returnValue(1));
        $connection->expects($this->once())
                   ->method('getDatabasePlatform')
                   ->will($this->returnCallback(function() {
                        return new MySqlPlatform;
                   }));

        $options = $this->getOptions($connection);
        $options->set('foo', 'bar');

        $this->assertEquals('bar', $options->get('foo'));
    }

    public function testSetWithAutoload()
    {
        $connection = $this->getConnection();
        $connection->expects($this->once())
                   ->method('executeQuery')
                   ->will($this->returnValue(1));
        $connection->expects($this->once())
                   ->method('getDatabasePlatform')
                   ->will($this->returnCallback(function() {
                        return new MySqlPlatform;
                   }));

        $options = $this->getOptions($connection);
        $options->set('foo', 'bar', true);

        $this->assertEquals('bar', $options->get('foo'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetEmptyOptionName()
    {
        $options = $this->getOptions();
        $options->set(null, null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetProtectedOption()
    {
        $options = $this->getOptions();
        $options->set('Autoload', null);
    }

    public function testRemove()
    {
        $connection = $this->getConnection();
        $connection->expects($this->once())
                   ->method('delete')
                   ->will($this->returnValue(1));

        $options = $this->getOptions($connection);
        $options->set('foo', 'bar');

        $this->assertEquals('bar', $options->get('foo'));

        $options->remove('foo');

        $this->assertNull($options->get('foo'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRemoveEmptyOptionName()
    {
        $options = $this->getOptions();
        $options->remove(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRemoveProtectedOption()
    {
        $options = $this->getOptions();
        $options->remove('Autoload');
    }

    protected function getConnection()
    {
        $mock = $this
            ->getMockBuilder('Pagekit\Database\Connection')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'fetchAssoc',
                    'fetchAll',
                    'executeQuery',
                    'getDatabasePlatform',
                    'update',
                    'insert',
                    'delete',
                    'isConnected',
                ]
            )
            ->getMock();

        $mock->method('isConnected')
             ->will($this->returnValue(true));

        return $mock;
    }

    protected function getCache()
    {
        return new ArrayCache();
    }

    protected function getOptions($connection = null, $cache = null)
    {
        $connection = $connection ?: $this->getConnection();
        $cache = $cache ?: $this->getCache();

        return new Option($connection, $cache, 'test');
    }
}
