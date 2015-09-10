<?php

namespace Pagekit\Auth\Tests\Fixtures;

use Pagekit\Auth\UserInterface;
use Pagekit\Auth\UserProviderInterface;

class UserProvider implements UserProviderInterface
{	
	protected $user;

	public function __construct(UserInterface $user)
	{
		$this->user = $user;
	}

	public function find($id)
	{

	}

	public function findByUsername($username)
	{
	}

	public function findByCredentials(array $credentials)
	{
		if (($this->user->getUserName() == $credentials['username']) && ($this->user->getPassword() == $credentials['password']))
		{
			return $this->user;
		}

		return null;
	}

	public function validateCredentials(UserInterface $user, array $credentials)
	{
		if ($user->getUserName($credentials['username']) && $user->getPassword($credentials['password']))
		{
			return true;
		}

		return false;
	}
}
