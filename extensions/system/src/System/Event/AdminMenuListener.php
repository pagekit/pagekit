<?php

namespace Pagekit\System\Event;

use Pagekit\Application as App;
use Pagekit\Menu\Event\MenuEvent;
use Pagekit\System\Menu\Item;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdminMenuListener implements EventSubscriberInterface
{
    /**
     * Adds extensions menu items.
     */
    public function onAdminMenu(MenuEvent $event)
    {
        $menu = $event->getMenu();
        $meta = App::user()->get('admin.menu', []);

        foreach (App::extension() as $extension) {
            foreach ($extension->getConfig('menu', []) as $id => $properties) {

                $properties['parentId'] = isset($properties['parent']) ? $properties['parent'] : 0;
                unset($properties['parent']);

                if (isset($meta[$id])) {
                    $properties['priority'] = $meta[$id];
                }

                if (!isset($properties['priority'])) {
                    $properties['priority'] = 100;
                }

                $menu->addItem(new Item(array_merge($properties, ['id' => $id, 'name' => isset($properties['label']) ? $properties['label'] : $id, 'menu' => $menu])));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.admin_menu' => 'onAdminMenu'
        ];
    }
}
