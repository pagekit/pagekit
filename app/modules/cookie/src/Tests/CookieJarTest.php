<?php

namespace Pagekit\Cookie\Tests;

use Pagekit\Cookie\CookieJar;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

class CookieJarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Cookie
     */
    protected $cookie;

    /**
     * @var CookieJar
     */
    protected $cookieJar;

	public function setUp() {
		$request = new Request([], [], [], ['testCookie' => 'testValue']);
		$this->cookieJar = new CookieJar($request);
	}

	public function testHasGet() {
		$this->assertTrue($this->cookieJar->has('testCookie'));
		$this->assertEquals('testValue', $this->cookieJar->get('testCookie'));
	}

	public function testSet() {
		$this->cookie = $this->cookieJar->set('aCookie', $this->cookieJar->get('testCookie'));
		$this->assertInstanceOf('Symfony\Component\HttpFoundation\Cookie', $this->cookie);
		$this->assertEquals('testValue', $this->cookie->getValue());
	}

	public function testRemove() {
		$this->cookie = $this->cookieJar->remove('aCookie');
		$this->assertInstanceOf('Symfony\Component\HttpFoundation\Cookie', $this->cookie);
		$this->assertTrue($this->cookie->isCleared());
	}

	public function testGetQueuedCookies() {
		$request = new Request();
		$this->cookieJar = new CookieJar($request);

		$this->cookieJar->set('cookie1', 'value1');
		$this->cookieJar->set('cookie2', 'value2');
		$this->assertCount(2, $this->cookieJar->getQueuedCookies());
	}
}
