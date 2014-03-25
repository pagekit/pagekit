<?php

namespace Pagekit\Menu\Event;

use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\System\Event\RegisterLinkEvent;
use Pagekit\System\Event\SystemInitEvent;

class MenuListener extends EventSubscriber
{
    /**
     * Sets the active menu items.
     *
     * @param SystemInitEvent $event
     */
    public function onSiteInit(SystemInitEvent $event)
    {
        $url   = $this('url');
        $attr  = $event->getRequest()->attributes;
        $path  = $event->getRequest()->getPathInfo();
        $route = ($attr->get('_main_route') ?: $attr->get('_route')).'%';

        $query = $this('menus')->getItemRepository()->query()
            ->orWhere(array('url = :path', 'url LIKE :route'), compact('path', 'route'));

        if ('/' == $path) {
            $query->orWhere('url = :front', array('front' => '@frontpage'));
        }

        if ($alias = $attr->get('_system_path')) {
            $query->orWhere('url = :alias', compact('alias'));
        }

        $active = array_filter($query->get(), function ($item) use ($url) {
            return $url->current() == $url->route($item->getUrl());
        });

        $attr->set('_menu', array_keys($active));
    }

    /**
     * Register link types for menu edit.
     *
     * @param RegisterLinkEvent $event
     */
    public function onRegisterLink(RegisterLinkEvent $event)
    {
        if (!$event->getContext() == 'system/menu') {
            return;
        }

        $event->register('Pagekit\Menu\Link\Divider');
        $event->register('Pagekit\Menu\Link\Header');
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'site.init' => array('onSiteInit', 16),
            'link.register' => 'onRegisterLink'
        );
    }
}
