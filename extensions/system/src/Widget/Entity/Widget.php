<?php

namespace Pagekit\Widget\Entity;

use Pagekit\User\Model\RoleInterface;
use Pagekit\User\Model\UserInterface;
use Pagekit\Widget\Model\Widget as BaseWidget;

/**
 * @Entity(tableClass="@system_widget", eventPrefix="system.widget")
 */
class Widget extends BaseWidget
{
    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column(type="string") */
    protected $type;

    /** @Column */
    protected $title = '';

    /** @Column */
    protected $position = '';

    /** @Column(type="integer") */
    protected $priority = 0;

    /** @Column(type="boolean") */
    protected $status;

    /** @Column(type="text") */
    protected $pages = '';

    /** @Column(name="menu_items", type="simple_array") */
    protected $menuItems = array();

    /** @Column(type="simple_array") */
    protected $roles = array();

    /** @Column(type="json_array", name="data") */
    protected $settings = array();

    public function getShowTitle()
    {
        return (bool) $this->get('show_title', true);
    }

    public function setShowTitle($showTitle)
    {
        $this->set('show_title', (bool) $showTitle);
    }

    public function getPriority()
    {
        return (int) $this->priority;
    }

    public function setPriority($priority)
    {
        $this->priority = (int) $priority;
    }

    public function getPages()
    {
        return $this->pages;
    }

    public function setPages($pages)
    {
        $this->pages = $pages;
    }

    public function getMenuItems()
    {
        return (array) $this->menuItems;
    }

    public function setMenuItems($menuItems)
    {
        $this->menuItems = $menuItems;
    }

    public function hasMenuItem($id)
    {
        return in_array($id, $this->getMenuItems());
    }


    public function hasAccess(UserInterface $user)
    {
        return !$roles = $this->getRoles() or array_intersect(array_keys($user->getRoles()), $roles);
    }

    /**
     * @param  RoleInterface $role
     * @return bool
     */
    public function hasRole(RoleInterface $role)
    {
        return in_array($role->getId(), $this->getRoles());
    }

    /**
     * @return int[]
     */
    public function getRoles()
    {
        return (array) $this->roles;
    }

    /**
     * @param $roles int[]
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    public function getStatusText()
    {
        $statuses = self::getStatuses();

        return isset($statuses[$this->status]) ? $statuses[$this->status] : __('Unknown');
    }

    public static function getStatuses()
    {
        return array(
            self::STATUS_DISABLED => __('Disabled'),
            self::STATUS_ENABLED  => __('Enabled')
        );
    }
}
