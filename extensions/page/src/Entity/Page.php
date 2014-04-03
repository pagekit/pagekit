<?php

namespace Pagekit\Page\Entity;
use Pagekit\Component\Database\ORM\EntityManager;

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
    protected $slug;

    /** @Column(type="string") */
    protected $title;

    /** @Column(type="boolean") */
    protected $status;

    /** @Column */
    protected $content = '';

    /** @Column(type="integer") */
    protected $access_id;

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

    /**
     * Ensure unique slug.
     *
     * @PreSave
     */
    public function postSave(EntityManager $manager)
    {
        $repository = $manager->getRepository('Pagekit\Page\Entity\Page');

        $i = 2;
        $id = $this->id;
        while ($repository->query()->where('slug = ?', array($this->slug))->where(function($query) use($id) { if ($id) $query->where('id <> ?', array($id)); })->first()) {
            $this->slug = preg_replace('/-\d+$/', '', $this->slug).'-'.$i++;
        }
    }
}
