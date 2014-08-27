<?php

namespace Pagekit\Page\Entity;

use Pagekit\System\Entity\DataTrait;
use Pagekit\User\Entity\AccessTrait;

/**
 * @Entity(tableClass="@page_page", eventPrefix="page.page")
 */
class Page
{
    use AccessTrait, DataTrait;

    /* Page default date. */
    const DEFAULT_DATE = '0000-00-00 00:00:00';

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

    /** @Column(type="json_array") */
    protected $data;

    /** @Column(type="datetime")*/
    protected $publish_up;

    /** @Column(type="datetime")*/
    protected $publish_down;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
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

    public function getPublishUp()
    {
        return $this->publish_up;
    }

    public function setPublishUp(\DateTime $publishUp)
    {
        $this->publish_up = $publishUp;
    }

    public function getPublishDown()
    {
        $defaultDate = new \DateTime(self::DEFAULT_DATE, new \DateTimeZone('UTC'));
        return ($this->publish_down != $defaultDate) ? $this->publish_down : false;    
    }


    public function setPublishDown(\DateTime $publishDown)
    {
        $this->publish_down = $publishDown;
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

    public static function getStatuses()
    {
        return [
            self::STATUS_UNPUBLISHED => __('Unpublished'),
            self::STATUS_PUBLISHED   => __('Published')
        ];
    }
}
