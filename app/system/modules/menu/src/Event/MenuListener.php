<?php

namespace Pagekit\Menu\Event;

use Doctrine\Common\Cache\Cache;
use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;
use Pagekit\Menu\Entity\Item;

class MenuListener implements EventSubscriberInterface
{
    /**
     * The menu item cache.
     *
     * @var Cache
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
     * @param Cache $cache
     */
    public function __construct(Cache $cache = null)
    {
        $this->cache = $cache ?: App::cache();
    }

    /**
     * Sets the active menu items.
     *
     * @param $event
     */
    public function onSystemSite($event)
    {
        $event->getRequest()->attributes->set('_menu', App::trigger(new ActiveMenuEvent($this->getItems()))->getActive());
    }

    /**
     * Activates menu items by current path.
     *
     * @param ActiveMenuEvent $event
     */
    public function onSystemMenu(ActiveMenuEvent $event)
    {
        $request  = App::request();
        $route    = $request->attributes->get('_route');
        $internal = App::url($route, $request->attributes->get('_route_params', []), 'link');

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
            foreach (Item::where(['status' => Item::STATUS_ENABLED])->get() as $item) {
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
    public function subscribe()
    {
        return [
            'system.site'                => 'onSystemSite',
            'system.menu'                => 'onSystemMenu',
            'system.menuitem.postSave'   => 'clearCache',
            'system.menuitem.postDelete' => 'clearCache',
            'system.node.postSave'       => 'clearCache',
            'system.node.postDelete'     => 'clearCache'
        ];
    }
}
