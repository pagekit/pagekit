<?php

namespace Pagekit\Extension\Event;

use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\System\Event\AdminMenuEvent;
use Pagekit\System\Menu\Item;

class AdminMenuListener extends EventSubscriber
{
    /**
     * Adds extensions menu items.
     */
    public function onAdminMenu(AdminMenuEvent $event)
    {
        $menu = $event->getMenu();

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
            'system.admin_menu' => 'onAdminMenu'
        );
    }
}
