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
    protected $cacheKey = 'menu.menuitems';

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
     */
    public function onSiteLoaded()
    {
        $this('request')->attributes->set('_menu', $this('events')->trigger('system.menu', new ActiveMenuEvent($this->getItems()))->getActive());
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

        $current = ltrim($request->getPathInfo(), '/');
        $paths   = array($current, $attr->get('_route_options[_main_route]', false, true) ?: $attr->get('_route'));

        if ($alias = ltrim($attr->get('_system_path'), '/')) {
            $paths[] = $alias;
        }

        foreach ($event->get($paths) as $id => $path) {
            if (0 === strpos($current, $url->route($path, array(), 'base'))) {
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
            $this->cacheDirty = true;
        }

        return $this->cacheEntries;
    }

    public function clearCache()
    {
        $this->cacheEntries = false;
        $this->cacheDirty   = true;
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
            'site.loaded'                => 'onSiteLoaded',
            'system.link'                => 'onSystemLink',
            'system.menu'                => 'onSystemMenu',
            'system.menuitem.postSave'   => 'clearCache',
            'system.menuitem.postDelete' => 'clearCache'
        );
    }
}
