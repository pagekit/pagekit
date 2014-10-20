<?php

namespace Pagekit\Menu\Event;

use Pagekit\Component\Cache\CacheInterface;
use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\Menu\Model\ItemInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class MenuListener extends EventSubscriber
{
    /**
     * The menu item cache.
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var string
     */
    protected $cacheKey = 'menu.menuitems';

    /**
     * @var array
     */
    protected $cacheEntries = false;

    /**
     * Constructor.
     *
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache = null)
    {
        $this->cache = $cache ?: $this['cache'];
    }

    /**
     * Sets the active menu items.
     *
     * @param GetResponseEvent $event
     */
    public function onSystemSite(GetResponseEvent $event)
    {
        $event->getRequest()->attributes->set('_menu', $this['events']->dispatch('system.menu', new ActiveMenuEvent($this->getItems()))->getActive());
    }

    /**
     * Activates menu items by current path.
     *
     * @param ActiveMenuEvent $event
     */
    public function onSystemMenu(ActiveMenuEvent $event)
    {
        $request  = $this['request'];
        $route    = $request->attributes->get('_route');
        $internal = $this['url']->route($route, $request->attributes->get('_route_params', []), 'link');

        foreach ($event->get($route) as $id => $path) {
            if ($path == $internal || substr($path, strlen($internal), 1) == '&') {
                $event->add($id);
            }
        }

        $query   = $request->getQueryString();
        $path    = $request->getPathInfo();
        $current = $path.($query ? '?' . $query : '');

        foreach ($event->get($path) as $id => $path) {
            $event->add($id);
        }

        $event->match($current);
    }

    /**
     * Gets menu items url info from cache.
     *
     * @return array
     */
    protected function getItems()
    {
        if (false === $this->cacheEntries = $this->cache->fetch($this->cacheKey)) {
            $this->cacheEntries = ['paths' => [], 'patterns' => []];
            foreach ($this['menus']->getItemRepository()->query()->where(['status' => ItemInterface::STATUS_ENABLED])->get() as $item) {
                if (!$item->getPages()) {
                    $this->cacheEntries['paths'][strtok(strtok($item->getUrl(), '?'), '#')][$item->getId()] = $item->getUrl();
                } else {
                    $this->cacheEntries['patterns'][$item->getId()] = $item->getPages();
                }
            }
            $this->cache->save($this->cacheKey, $this->cacheEntries);
        }

        return $this->cacheEntries;
    }

    /**
     * Clears the cache.
     */
    public function clearCache()
    {
        $this->cache->delete($this->cacheKey);
        $this->cacheEntries = false;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.site'                => 'onSystemSite',
            'system.menu'                => 'onSystemMenu',
            'system.menuitem.postSave'   => 'clearCache',
            'system.menuitem.postDelete' => 'clearCache',
            'system.alias.postSave'      => 'clearCache',
            'system.alias.postDelete'    => 'clearCache'
        ];
    }
}