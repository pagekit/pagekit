<?php

namespace Pagekit\Menu\Event;

use Pagekit\Component\Cache\CacheInterface;
use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\Menu\Model\ItemInterface;
use Pagekit\System\Event\LinkEvent;
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
        $this->cache = $cache ?: $this('cache');
    }

    /**
     * Register link types for menu edit.
     *
     * @param LinkEvent $event
     */
    public function onSystemLink(LinkEvent $event)
    {
        if (!$event->getContext() == 'system/menu') {
            return;
        }

        $event->register('Pagekit\Menu\Link\Divider');
        $event->register('Pagekit\Menu\Link\Header');
    }

    /**
     * Sets the active menu items.
     *
     * @param GetResponseEvent $event
     */
    public function onSiteLoaded(GetResponseEvent $event)
    {
        $event->getRequest()->attributes->set('_menu', $this('events')->dispatch('system.menu', new ActiveMenuEvent($this->getItems()))->getActive());
    }

    /**
     * Activates menu items by current path.
     *
     * @param ActiveMenuEvent $event
     */
    public function onSystemMenu(ActiveMenuEvent $event)
    {
        $url     = $this('url');
        $request = $this('request');
        $attr    = $request->attributes;

        $query   = $request->getQueryString();
        $path    = ltrim($request->getPathInfo(), '/');
        $current = $path.($query ? '?' . $query : '');
        $paths   = array($path, $attr->get('_route_options[_main_route]', false, true) ?: $attr->get('_route'));

        if ($alias = ltrim($attr->get('_system_path'), '/')) {
            $paths[] = $alias;
        }

        foreach ($event->get($paths) as $id => $path) {
            if ($route = $url->route($path, array(), 'base') and ($current == $route
                || (0 === strpos($current, $route) && strpbrk($current[strlen($route)], '/?'))
                || (0 === strpos($route, $current) && strpbrk($route[strlen($current)], '/?')))) {

                $event->add($id);
            }
        }

        $event->match($current);
    }

    /**
     * Gets menu items url info from cache
     *
     * @return array
     */
    protected function getItems()
    {
        if (false === $this->cacheEntries = $this->cache->fetch($this->cacheKey)) {
            $this->cacheEntries = array('paths' => array(), 'patterns' => array());
            foreach ($this('menus')->getItemRepository()->query()->where(array('status' => ItemInterface::STATUS_ACTIVE))->get() as $item) {
                if (!$item->getPages()) {
                    $this->cacheEntries['paths'][strtok($item->getUrl(), '?')][$item->getId()] = $item->getUrl();
                } else {
                    $this->cacheEntries['patterns'][$item->getId()] = $item->getPages();
                }
            }
            $this->cache->save($this->cacheKey, $this->cacheEntries);
        }

        return $this->cacheEntries;
    }

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
        return array(
            'site.loaded'                => 'onSiteLoaded',
            'system.link'                => 'onSystemLink',
            'system.menu'                => 'onSystemMenu',
            'system.menuitem.postSave'   => 'clearCache',
            'system.menuitem.postDelete' => 'clearCache',
            'system.alias.postSave'   => 'clearCache',
            'system.alias.postDelete' => 'clearCache'
        );
    }
}
