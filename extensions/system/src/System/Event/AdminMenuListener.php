<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\Menu\Event\MenuEvent;
use Pagekit\Menu\Model\Menu as AdminMenu;
use Pagekit\System\Menu\Item;

class AdminMenuListener extends EventSubscriber
{
    /**
     * Creates the menu instance and dispatches the 'admin.menu' event.
     */
    public function onAdminInit()
    {
        $menu = new AdminMenu;
        $menu->setId('admin');

        $this('menus')->set($menu);
        $this('menus')->registerFilter('access', 'Pagekit\System\Menu\Filter\AccessFilter', 16);
        $this('menus')->registerFilter('active', 'Pagekit\System\Menu\Filter\ActiveFilter');

        $this('events')->trigger('admin.menu', new MenuEvent($menu));
    }

    /**
     * Reads the 'menu' extension configuration and creates the menu items.
     */
    public function onMenu()
    {
        $menu = $this('menus')->get('admin');

        foreach ($this('extensions') as $extension) {
            foreach ($extension->getConfig('menu', array()) as $id => $properties) {

                $properties['parentId'] = isset($properties['parent']) ? $properties['parent'] : 0;
                unset($properties['parent']);

                $menu->addItem(new Item(array_merge($properties, array('id' => $id, 'name' => isset($properties['label']) ? $properties['label'] : $id, 'menu' => $menu))));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'admin.init' => 'onAdminInit',
            'admin.menu' => 'onMenu'
        );
    }
}
