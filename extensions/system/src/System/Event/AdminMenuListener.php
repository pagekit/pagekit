<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\Menu\Model\Menu;

class AdminMenuListener extends EventSubscriber
{
    /**
     * Creates the menu instance and dispatches the 'admin.menu' event.
     */
    public function onAdminInit()
    {
        $menu = new Menu;
        $menu->setId('admin');

        $this('menus')->set($menu);
        $this('menus')->registerFilter('access', 'Pagekit\System\Menu\Filter\AccessFilter', 16);
        $this('menus')->registerFilter('active', 'Pagekit\System\Menu\Filter\ActiveFilter');

        $this('events')->trigger('admin.menu', new AdminMenuEvent($menu));
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'admin.init' => 'onAdminInit'
        );
    }
}
