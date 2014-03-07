<?php

namespace Pagekit\User;

use Pagekit\Component\Cache\CacheInterface;
use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\ApplicationAware;
use Pagekit\User\Entity\UserRepository;
use Pagekit\User\Helper\AccessLevelHelper;
use Pagekit\User\Model\UserInterface;

class UserProvider extends ApplicationAware
{
    /**
     * @var AccessLevelHelper
     */
    protected $access;
    /**
     * Constructor.
     */
    public function __construct(CacheInterface $cache = null)
    {
        $this('events')->addSubscriber($this->access = new AccessLevelHelper($this->getAccessLevelRepository(), $cache));
    }

    /**
     * @return UserRepository
     */
    public function getUserRepository()
    {
        return $this('db.em')->getRepository('Pagekit\User\Entity\User');
    }

    /**
     * @return Repository
     */
    public function getRoleRepository()
    {
        return $this('db.em')->getRepository('Pagekit\User\Entity\Role');
    }

    /**
     * @return Repository
     */
    public function getAccessLevelRepository()
    {
        return $this('db.em')->getRepository('Pagekit\User\Entity\AccessLevel');
    }

    /**
     * Gets a user by id.
     *
     * @param  string|null $id The user id to retrieve or null for current user
     * @return UserInterface|null
     */
    public function get($id = null)
    {
        return $id === null ? $this('user') : $this->getUserRepository()->find($id);
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

    /**
     * Returns true if user has access
     *
     * @param  int           $id   Access level id
     * @param  UserInterface $user
     * @return bool
     */
    public function checkAccessLevel($id, UserInterface $user = null)
    {
        if (!$user) {
            $user = $this->get();
        }

        return $this->access->checkAccessLevel($id, $user);
    }
}
