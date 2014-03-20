<?php

namespace Pagekit\Menu\Event;

use Pagekit\Framework\Event\EventSubscriber;

class MenuListener extends EventSubscriber
{
    /**
     * Sets the active menu items
     *
     * @param $event
     */
    public function onSiteInit($event)
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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'site.init' => array('onSiteInit', 16)
        );
    }
}
