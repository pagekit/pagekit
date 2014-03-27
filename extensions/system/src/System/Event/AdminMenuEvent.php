<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\Event;
use Pagekit\Menu\Model\MenuInterface;

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
}
