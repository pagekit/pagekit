<?php

namespace Pagekit\System\Event;

use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;
use Pagekit\Finder\Event\FileAccessEvent;
use Pagekit\Menu\Event\MenuEvent;
use Pagekit\Menu\Model\Menu;

class SystemListener implements EventSubscriberInterface
{
    /**
     * Dispatches the 'app.site' or 'app.admin' event.
     */
    public function onRequest($event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        App::trigger(App::isAdmin() ? 'app.admin' : 'app.site', [App::request()]);
    }

    /**
     * Creates the menu instance and dispatches the 'system.admin_menu' event.
     */
    public function onAdmin()
    {
        $menu = new Menu;
        $menu->setId('admin');

        App::menus()->registerFilter('access', 'Pagekit\System\Menu\Filter\AccessFilter', 16);
        App::menus()->registerFilter('active', 'Pagekit\System\Menu\Filter\ActiveFilter');

        App::trigger(new MenuEvent('system.admin_menu', $menu));

        App::set('admin.menu', App::menus()->getTree($menu, ['access' => true]));
    }

    /**
     * Registers the media storage folder
     *
     * @param FileAccessEvent $event
     */
    public function onSystemFinder(FileAccessEvent $event)
    {
        if (App::user()->hasAccess('system: manage storage | system: manage storage read only')) {
            $event->path('#^'.strtr(App::get('path.storage'), '\\', '/').'($|\/.*)#', App::user()->hasAccess('system: manage storage') ? 'w' : 'r');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'app.request'   => 'onRequest',
            'app.admin'     => 'onAdmin',
            'system.finder' => 'onSystemFinder'
        ];
    }
}
