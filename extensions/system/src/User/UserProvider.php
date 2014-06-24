<?php

namespace Pagekit\User;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\ApplicationTrait;
use Pagekit\User\Entity\UserRepository;
use Pagekit\User\Model\UserInterface;

class UserProvider implements \ArrayAccess
{
    use ApplicationTrait;

    /**
     * @return UserRepository
     */
    public function getUserRepository()
    {
        return $this['db.em']->getRepository('Pagekit\User\Entity\User');
    }

    /**
     * @return Repository
     */
    public function getRoleRepository()
    {
        return $this['db.em']->getRepository('Pagekit\User\Entity\Role');
    }

    /**
     * Gets a user by id.
     *
     * @param  string|null $id The user id to retrieve or null for current user
     * @return UserInterface|null
     */
    public function get($id = null)
    {
        return $id === null ? $this['user'] : $this->getUserRepository()->find($id);
    }

    /**
     * Gets a user by username.
     *
     * @param  string $username
     * @return UserInterface
     */
    public function getByUsername($username)
    {
        return $this->getUserRepository()->findByUsername($username);
    }
}
