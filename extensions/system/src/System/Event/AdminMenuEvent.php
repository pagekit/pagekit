<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\Event;
use Pagekit\Menu\Model\MenuInterface;
use Pagekit\System\Menu\Item;

class AdminMenuEvent extends Event
{
    /**
     * @var MenuInterface
     */
    protected $menu;

    /**
     * Constructor.
     *
     * @param MenuInterface $menu
     */
    public function __construct(MenuInterface $menu)
    {
        $this->menu = $menu;
    }

    /**
     * Gets the menu instance.
     *
     * @return MenuInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Adds menu items to the menu instance.
     *
     * @param array $items
     */
    public function addItems(array $items)
    {
        foreach ($items as $id => $properties) {

            $properties['parentId'] = isset($properties['parent']) ? $properties['parent'] : 0;
            unset($properties['parent']);

            $this->menu->addItem(new Item(array_merge($properties, array('id' => $id, 'name' => isset($properties['label']) ? $properties['label'] : $id, 'menu' => $this->menu))));
        }
    }
}
