<?php

namespace Pagekit\User\Helper;

use Pagekit\Component\Cache\CacheInterface;
use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Component\Event\EventSubscriberInterface;
use Pagekit\User\Model\UserInterface;

class AccessLevelHelper implements EventSubscriberInterface
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
     * @var Repository
     */
    protected $levels;

    /**
     * Constructor.
     *
     * @param Repository     $levels
     * @param CacheInterface $cache
     */
    public function __construct(Repository $levels, CacheInterface $cache = null)
    {
        $this->levels = $levels;

        if ($this->cache = $cache) {
            $this->cacheEntries = $cache->fetch($this->cacheKey);
        }
    }

    /**
     * Returns true if user has access
     *
     * @param  int           $id   Access level id
     * @param  UserInterface $user
     * @return bool
     */
    public function checkAccessLevel($id, UserInterface $user)
    {
        if (!isset($this->cacheEntries[$id])) {

            if (!$access = $this->levels->find($id)) {
                return false;
            }

            $this->cacheEntries[$id] = $access->getRoles();
            $this->cacheDirty = true;
        }

        return !$this->cacheEntries[$id] || array_intersect(array_keys($user->getRoles()), $this->cacheEntries[$id]);
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
