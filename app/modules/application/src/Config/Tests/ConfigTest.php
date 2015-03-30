<?php

namespace Pagekit\Config\Tests;

use Pagekit\Config\Config;
use Pagekit\Config\Loader\PhpLoader;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    protected $config;

    public function setUp()
	{
	    $this->config = new Config;
	    $this->config->load(__DIR__.'/Fixtures/config.php');
	}

	public function testHas()
	{
		$this->assertTrue(!$this->config->has('Foo'));
		$this->assertTrue($this->config->has('app'));
	}

	public function testGet()
	{
		$this->assertEquals($this->config->get('app.site_title'), 'Demo');
	}

	public function testLoadReplace()
	{
		$config = new Config(['replacement' => 'de']);
		$config->load(__DIR__.'/Fixtures/config2.php');

		$this->assertEquals($config->get('app.locale'), 'de');
		$this->assertEquals($config->get('profiler.enabled'), '0');
	}

	public function testGetValues()
	{
		$loader = new PhpLoader;

		if ($supports = $loader->supports(__DIR__.'/Fixtures/config.php')) {
			$config = $loader->load(__DIR__.'/Fixtures/config.php');
			$this->assertEquals($this->config->getValues(), $config);
		}

		$this->assertTrue($supports);
	}

	public function testSet()
	{
		$this->config->load(__DIR__.'/Fixtures/config2.php');
		$this->config->set('mail.from.address', 'admin@test.de');
		$this->assertEquals($this->config->get('mail.from.address'), 'admin@test.de');
		$this->config->offsetSet('foo.bar', 'fooBar');
		$this->assertTrue($this->config->offsetExists('foo.bar'));
		$this->assertEquals($this->config->offsetGet('foo.bar'), 'fooBar');
		$this->config->offsetUnset('foo.bar');
		$this->assertEquals($this->config->offsetGet('foo.bar'), null);
	}

	public function testDump()
	{
		$this->config->dump();
	}
}
