<?php

namespace Pagekit\User;

use Pagekit\Component\Cache\CacheInterface;
use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\User\Entity\UserRepository;
use Pagekit\User\Model\RoleInterface;
use Pagekit\User\Model\UserInterface;

class UserProvider extends EventSubscriber
{
    /**
     * The access level cache.
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var bool
     */
    protected $cacheDirty = false;

    /**
     * @var string
     */
    protected $cacheKey = 'user.access.levels';

    /**
     * @var array
     */
    protected $cacheEntries = array();

    /**
     * Constructor.
     *
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache = null)
    {
        if ($this->cache = $cache) {
            $this->cacheEntries = $cache->fetch($this->cacheKey);
            $this('events')->addSubscriber($this);
        }
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
    public function hasAccess($id, UserInterface $user = null)
    {
        if (!$user) {
            $user = $this->get();
        }

        if (!isset($this->cacheEntries[$id])) {

            if (!$access = $this->getAccessLevelRepository()->find($id)) {
                return true;
            }

            $this->cacheEntries[$id] = $access->getRoles();
            $this->cacheDirty = true;
        }

        return !$this->cacheEntries[$id]
            or in_array(RoleInterface::ROLE_AUTHENTICATED, $this->cacheEntries[$id]) and $user->isAuthenticated()
            or array_intersect(array_keys($user->getRoles()), $this->cacheEntries[$id]);
    }

    public function clearCache()
    {
        $this->cacheEntries = array();
        $this->cacheDirty = true;
    }

    public function __destruct()
    {
        if ($this->cache && $this->cacheDirty) {
            $this->cache->save($this->cacheKey, $this->cacheEntries);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'system.accesslevel.postSave'   => 'clearCache',
            'system.accesslevel.postDelete' => 'clearCache'
        );
    }
}
