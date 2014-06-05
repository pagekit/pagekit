<?php

namespace Pagekit\Page\Entity;
use Pagekit\User\Model\RoleInterface;
use Pagekit\User\Model\UserInterface;

/**
 * @Entity(tableClass="@page_page", eventPrefix="page.page")
 */
class Page
{
    /* Page unpublished status. */
    const STATUS_UNPUBLISHED = 0;

    /* Page published status. */
    const STATUS_PUBLISHED = 1;

    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column(type="string") */
    protected $url;

    /** @Column(type="string") */
    protected $title;

    /** @Column(type="integer") */
    protected $status = self::STATUS_UNPUBLISHED;

    /** @Column */
    protected $content = '';

    /** @Column(type="simple_array") */
    protected $roles = array();

    /** @Column(type="json_array") */
    protected $data;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
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

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function get($key, $default = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public static function getStatuses()
    {
        return array(
            self::STATUS_UNPUBLISHED  => __('Unpublished'),
            self::STATUS_PUBLISHED => __('Published')
        );
    }

	public function getStatusText()
    {
		$statuses = self::getStatuses();

		return isset($statuses[$this->status]) ? $statuses[$this->status] : __('Unknown');
	}
}
