<?php

namespace Pagekit\Menu\Event;

use Pagekit\Application as App;
use Pagekit\Event\Event;
use Pagekit\Menu\Model\MenuInterface;
use Pagekit\System\Menu\Item;

class MenuEvent extends Event
{
    /**
     * @var MenuInterface
     */
    protected $menu;

    /**
     * Constructs an event.
     *
     * @param MenuInterface $menu
     */
    public function __construct($name, MenuInterface $menu)
    {
        parent::__construct($name);

        $this->menu = $menu;
    }

    /**
     * Returns the menu for this event.
     *
     * @return MenuInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Register menu config.
     *
     * @param $config
     */
    public function register(array $config)
    {
        $meta = App::user()->get('admin.menu', []);

        foreach ($config as $id => $properties) {

            $properties['parentId'] = isset($properties['parent']) ? $properties['parent'] : 0;
            unset($properties['parent']);

            if (isset($meta[$id])) {
                $properties['priority'] = $meta[$id];
            }

            if (!isset($properties['priority'])) {
                $properties['priority'] = 100;
            }

            if (!isset($properties['active'])) {
                $properties['active'] = $properties['url'];
            }

            $this->menu->addItem(new Item(array_merge($properties, ['id' => $id, 'name' => isset($properties['label']) ? $properties['label'] : $id, 'menu' => $this->menu])));
        }
    }
}
