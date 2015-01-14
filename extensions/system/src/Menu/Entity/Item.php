<?php

namespace Pagekit\Menu\Entity;

use Pagekit\Component\Database\ORM\ModelTrait;
use Pagekit\Menu\Model\Item as BaseItem;
use Pagekit\Menu\Model\MenuInterface;
use Pagekit\User\Entity\AccessTrait;

/**
 * @Entity(tableClass="@system_menu_item", eventPrefix="system.menuitem")
 */
class Item extends BaseItem
{
    use AccessTrait, ModelTrait;

    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column(name="menu_id", type="integer") */
    protected $menuId;

    /** @Column(name="parent_id", type="integer") */
    protected $parentId = 0;

    /** @Column(type="string") */
    protected $name;

    /** @Column(type="string") */
    protected $url;

    /** @Column(type="smallint") */
    protected $status;

    /** @Column(type="integer") */
    protected $priority = 0;

    /** @Column(type="integer") */
    protected $depth = 0;

    /** @Column(type="text") */
    protected $pages = '';

    /** @Column(type="json_array") */
    protected $data;

    /**
     * @var Menu
     * @BelongsTo(targetEntity="Menu", keyFrom="menu_id")
     */
    protected $menu;

    /**
     * @return string
     */
    public function getMenuId()
    {
        return $this->menuId;
    }

    /**
     * @param string $menuId
     */
    public function setMenuId($menuId)
    {
        $this->menuId = $menuId;
    }

    /**
     * @param MenuInterface $menu
     */
    public function setMenu(MenuInterface $menu)
    {
        $this->menu = $menu;
        $this->setMenuId($menu->getId());
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatusText()
    {
        $statuses = self::getStatuses();

        return isset($statuses[$this->status]) ? $statuses[$this->status] : __('Unknown');
    }

    public function getDepth()
    {
        return $this->depth;
    }

    public function setDepth($depth)
    {
        $this->depth = $depth;
    }

    public function setPages($pages)
    {
        $this->pages = $pages;
    }

    public function getPages()
    {
        return $this->pages;
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_DISABLED  => __('Disabled'),
            self::STATUS_ENABLED   => __('Enabled')
        ];
    }

    /**
     * @PreSave
     */
    public function preSave()
    {
        if (!$this->id) {
            $this->setPriority(self::getConnection()->fetchColumn('SELECT MAX(priority) + 1 FROM @system_menu_item WHERE menu_id=? AND DEPTH=0', [$this->getMenuId()]) ?: 0);
        }
    }

    public static function findByMenu($menu)
    {
        $menu_id = ($menu instanceof MenuInterface) ? $menu->getId() : (int) $menu;
        return self::where(compact('menu_id'))->get();
    }
}
