<?php

namespace Pagekit\Auth\Tests;

use Pagekit\Auth\Auth;
use Pagekit\Auth\AuthEvents;
use Pagekit\Auth\Event\LoginEvent;
use Pagekit\Auth\Event\LogoutEvent;
use Pagekit\Auth\Tests\Fixtures\User;
use Pagekit\Auth\Tests\Fixtures\UserProvider;
use Pagekit\Event\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;

class AuthTest extends \PHPUnit_Framework_TestCase
{
	protected $auth;
	protected $user;

	public function setUp()
	{
		$this->events = new EventDispatcher;
		$this->session = $this->mockSession();
		$this->auth = new Auth($this->events, $this->session);
		$this->user = new User;
	}

	public function testSetGetUser()
	{
		$this->auth->setUser($this->user);
		$this->assertInstanceOf('Pagekit\Auth\UserInterface', $this->auth->getUser());
	}

	public function testGetUserUnexpiredToken()
	{
		$this->auth->refresh('validToken');
		$this->assertInstanceOf('Pagekit\Auth\UserInterface', $this->auth->getUser());
	}

	/**
	* @expectedException RuntimeException
	*/
	public function testGetUserProviderException()
	{
		$this->auth->getUserProvider();
	}

	public function testSetGetUserProvider()
	{
		$this->auth->setUserProvider($this->mockUserProvider());
		$this->assertInstanceOf('Pagekit\Auth\UserProviderInterface', $this->auth->getUserProvider());
	}

	public function testAuthenticate()
	{
		$this->auth->setUser($this->user);
		$provider = new UserProvider($this->user);
		$this->auth->setUserProvider($provider);

		$this->assertInstanceOf('Pagekit\Auth\Tests\Fixtures\User', $this->auth->authenticate(['username' => 'username', 'password' => 'password']));
	}

	/**
	* @expectedException Pagekit\Auth\Exception\BadCredentialsException
	*/
	public function testAuthenticateException()
	{
		$this->auth->setUser($this->user);
		$provider = new UserProvider($this->user);
		$this->auth->setUserProvider($provider);

		$this->auth->authenticate(['username' => 'wrongUsername', 'password' => 'password']);
	}

	public function testGetSession()
	{
		$this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Session', $this->auth->getSession());
	}

	public function testLogin()
	{
		$this->events->on(AuthEvents::LOGIN, function(LoginEvent $event) {
			$event->setResponse(new Response);
		});
		$response = $this->auth->login($this->user);
		$this->assertEquals(200, $response->getStatusCode());
	}

	public function testLogout()
	{
		$this->events->on(AuthEvents::LOGOUT, function(LogoutEvent $event) {
			$event->setResponse(new Response);
		});
		$response = $this->auth->logout($this->user);
		$this->assertEquals(200, $response->getStatusCode());
	}

	protected function mockUserProvider()
	{
		return $this->getMock('Pagekit\Auth\UserProviderInterface');
	}

	protected function mockSession()
	{
		$session = $this->getMock('Symfony\Component\HttpFoundation\Session\Session');
		$session->expects($this->any())
				->method('get')
				->will($this->returnCallback([$this, 'sessionCallback']));
		return $session;
	}

	public function sessionCallback()
	{
		$arg = func_get_arg(0);
		if ($arg == '_auth.user_'.sha1(get_class($this->auth)))
		{
			return new User;
		}
		if ($arg == '_auth.token_'.sha1(get_class($this->auth)))
		{
			return 'validToken';
		}
	}
}
