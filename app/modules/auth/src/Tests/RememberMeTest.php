<?php

namespace Pagekit\Auth\Tests;

use Pagekit\Auth\RememberMe;

class RememberMeTest extends \PHPUnit_Framework_TestCase
{
	protected $key;
	protected $username;
    protected $cookie;
    protected $provider;
    protected $remember;

	public function setUp()
	{
		$this->key = 'EeJoowaprM';
		$this->username = 'aName';
		$this->cookie = $this->getMockBuilder('Pagekit\Cookie\CookieJar')
							->disableOriginalConstructor()
							->getMock();
		$this->provider = $this->getMockBuilder('Pagekit\Auth\UserProviderInterface')
							->disableOriginalConstructor()
							->getMock();
		$this->remember = new RememberMe($this->key, $this->username, $this->cookie);
	}

	public function testAutologin()
	{
		$this->provider->expects($this->exactly(1))
					->method('findByUsername')
					->will($this->returnCallback(function() {
						$user = $this->getMock('Pagekit\Auth\UserInterface');
						$user->expects($this->any())
							->method('getPassword')
							->will($this->returnValue('aPassword'));
						return $user;
					}));

		$this->cookie->expects($this->exactly(1))
					->method('get')
					->will($this->returnCallback(function() {
						$encUsername = base64_encode($this->username);
						$time = time() + 60;
						$hash = sha1($this->username . $time . 'aPassword' . $this->key);
						return base64_encode($encUsername.':'.$time.':'.$hash);
					}));

		$this->assertInstanceOf('Pagekit\Auth\UserInterface', $this->remember->autoLogin($this->provider));
	}

	/**
     * @expectedException        Pagekit\Auth\Exception\AuthException
     * @expectedExceptionMessage No remember me cookie found.
     */
	public function testAutoLoginNoCookieException()
	{
		$this->remember->autoLogin($this->provider);
	}

	/**
     * @expectedException        Pagekit\Auth\Exception\AuthException
     * @expectedExceptionMessage The cookie is invalid.
     */
	public function testAutoLoginInvalidCookieException()
	{
		$this->cookie->expects($this->exactly(1))
					->method('get')
					->will($this->returnValue('something'));
		var_dump($this->remember->autoLogin($this->provider));
	}

	/**
     * @expectedException        Pagekit\Auth\Exception\AuthException
     * @expectedExceptionMessage The cookie has expired.
     */
	public function testAutoLoginCookieExpiredException()
	{
		$this->cookie->expects($this->exactly(1))
					->method('get')
					->will($this->returnCallback(function() {
						$username = base64_encode($this->username);
						$time = time() - 60;
						$hash = sha1($this->username . $time . 'aPassword' . $this->key);
						return base64_encode($username.':'.$time.':'.$hash);
					}));
		var_dump($this->remember->autoLogin($this->provider));
	}

	/**
     * @expectedException        Pagekit\Auth\Exception\AuthException
     * @expectedExceptionMessage "" contains a character from outside the base64 alphabet.
     */
	public function testAutoLoginInvalidUsernameException()
	{
		$this->cookie->expects($this->exactly(1))
					->method('get')
					->will($this->returnCallback(function() {
						$time = time() + 60;
						$hash = sha1('#+?' . $time . 'aPassword' . $this->key);
						return base64_encode('#+?'.':'.$time.':'.$hash);
					}));
		$this->remember->autoLogin($this->provider);
	}

	/**
     * @expectedException        Pagekit\Auth\Exception\AuthException
     * @expectedExceptionMessage No user found for "nobody"
     */
	public function testAutoLoginUserNotFoundException()
	{
		$this->provider->expects($this->exactly(1))
				->method('findByUsername')
				->will($this->returnValue(null));

		$this->cookie->expects($this->exactly(1))
					->method('get')
					->will($this->returnCallback(function() {
						$username = base64_encode('nobody');
						$time = time() + 60;
						$hash = sha1('nobody' . $time . 'aPassword' . $this->key);
						return base64_encode($username.':'.$time.':'.$hash);
					}));
		var_dump($this->remember->autoLogin($this->provider));
	}

	/**
     * @expectedException        Pagekit\Auth\Exception\AuthException
     * @expectedExceptionMessage The cookie's hash is invalid.
     */
	public function testAutoLoginInvalidHashException()
	{
		$this->provider->expects($this->exactly(1))
				->method('findByUsername')
				->will($this->returnCallback(function() {
					return $user = $this->getMock('Pagekit\Auth\UserInterface');
				}));

		$this->cookie->expects($this->any())
					->method('has')
					->will($this->returnValue(true));

		$this->cookie->expects($this->exactly(1))
					->method('get')
					->will($this->returnCallback(function() {
						$time = time() + 60;
						$hash = sha1('nobody' . $time . 'aPassword' . $this->key);
						return base64_encode('notEncodedUser'.':'.$time.':'.$hash);
					}));
		$this->remember->autoLogin($this->provider);
	}

	public function testSet()
	{
		$request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
						->disableOriginalConstructor()
						->getMock();
		$request->expects($this->atLeastOnce())
				->method('get')
				->will($this->returnValue('true'));

		$user = $this->getMockBuilder('Pagekit\Auth\UserInterface')
						->disableOriginalConstructor()
						->getMock();

		$this->cookie->expects($this->atLeastOnce())
					->method('set')
					->with($this->equalTo($this->username),
							$this->anything(),
							$this->greaterThan(RememberMe::COOKIE_LIFETIME)
						);


		$this->remember->set($request, $user);
	}
}
