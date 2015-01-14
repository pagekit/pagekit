<?php

namespace Pagekit\User;

use Pagekit\Framework\ApplicationTrait;
use Pagekit\User\Entity\User;
use Pagekit\User\Model\UserInterface;

class UserProvider implements \ArrayAccess
{
    use ApplicationTrait;

    /**
     * Gets a user by id.
     *
     * @param  string|null $id The user id to retrieve or null for current user
     * @return UserInterface|null
     */
    public function get($id = null)
    {
        return $id === null ? $this['user'] : User::find($id);
    }

    /**
     * Gets a user by username.
     *
     * @param  string $username
     * @return UserInterface
     */
    public function getByUsername($username)
    {
        return User::findByUsername($username);
    }
}
