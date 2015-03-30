<?php

namespace Pagekit\Auth\Tests;

use Pagekit\Auth\Encoder\NativePasswordEncoder;

class NativePasswordEncoderTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->passwordEncoder = new NativePasswordEncoder;
	}

	public function testHashVerify()
	{
		$iv = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
		$this->assertEquals(true, $this->passwordEncoder->verify($this->passwordEncoder->hash('password'), 'password'));
		$this->assertEquals(true, $this->passwordEncoder->verify($this->passwordEncoder->hash('password', $iv), 'password'));
		$this->assertEquals(false, $this->passwordEncoder->verify($this->passwordEncoder->hash('password'), 'notThePassword'));
	}

	/**
     * @expectedException	InvalidArgumentException
     */
	public function testVerifyException()
	{
		$this->passwordEncoder->verify($this->passwordEncoder->hash('password'), 'password', 'salt');
	}
}
