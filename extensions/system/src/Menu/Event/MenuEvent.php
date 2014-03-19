<?php

namespace Pagekit\Menu\Event;

use Pagekit\Framework\Event\Event;
use Pagekit\Menu\Model\MenuInterface;

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
    public function __construct(MenuInterface $menu)
    {
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
}