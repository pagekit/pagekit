<?php

namespace Pagekit\Menu\Entity;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Component\Menu\Model\MenuInterface;

class ItemRepository extends Repository
{
    public function findByMenu($menu)
    {
        $menu_id = ($menu instanceof MenuInterface) ? $menu->getId() : (int) $menu;
        return $this->where(compact('menu_id'))->get();
    }
}
