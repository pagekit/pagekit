<?php

namespace Pagekit\Menu\Event;

use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\System\Event\LinkEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class MenuListener extends EventSubscriber
{
    /**
     * Sets the active menu items.
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $url     = $this('url');
        $request = $event->getRequest();
        $attr    = $request->attributes;
        $path    = ltrim($request->getPathInfo(), '/');
        $route   = ($attr->get('_main_route') ?: $attr->get('_route')).'%';

        $query = $this('menus')->getItemRepository()->query()
            ->orWhere(array('url = :path', 'url LIKE :route'), compact('path', 'route'));

        if ('' == $path) {
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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'kernel.request' => 'onKernelRequest',
            'system.link'    => 'onSystemLink'
        );
    }
}
