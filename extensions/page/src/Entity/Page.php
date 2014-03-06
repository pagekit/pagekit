<?php

namespace Pagekit\Page\Entity;

/**
 * @Entity(tableClass="@page_page", eventPrefix="page.page")
 */
class Page
{
    /* Page draft status. */
    const STATUS_DISABLED = 0;

    /* Page published. */
    const STATUS_ENABLED = 1;

    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column(type="string") */
    protected $slug;

    /** @Column(type="string") */
    protected $title;

    /** @Column(type="boolean") */
    protected $status;

    /** @Column */
    protected $content = '';

    /** @Column(type="integer") */
    protected $access_id;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setAccessId($accessId)
    {
        $this->access_id = $accessId;
    }

    public function getAccessId()
    {
        return (int) $this->access_id;
    }

    public static function getStatuses()
    {
        return array(
            self::STATUS_ENABLED  => __('Enabled'),
            self::STATUS_DISABLED => __('Disabled')
        );
    }

	public function getStatusText()
    {
		$statuses = self::getStatuses();

		return isset($statuses[$this->status]) ? $statuses[$this->status] : __('Unknown');
	}
}
