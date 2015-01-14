<?php

namespace Pagekit\Menu\Entity;

use Pagekit\Component\Database\ORM\ModelTrait;
use Pagekit\Menu\Model\Menu as BaseMenu;

/**
 * @Entity(tableClass="@system_menu", eventPrefix="system.menu")
 */
class Menu extends BaseMenu
{
    use ModelTrait;

    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column(type="string") */
    protected $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
