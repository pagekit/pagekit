<?php

namespace Pagekit\Site\Model;

use Pagekit\Application as App;
use Pagekit\System\Model\DataTrait;
use Pagekit\System\Model\NodeTrait;
use Pagekit\User\Model\AccessTrait;

/**
 * @Entity(tableClass="@system_node")
 */
class Node implements NodeInterface, \JsonSerializable
{
    use AccessTrait, DataTrait, NodeModelTrait, NodeTrait;

    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column(name="parent_id", type="integer") */
    protected $parentId = 0;

    /** @Column(type="integer") */
    protected $priority = 0;

    /** @Column(type="integer") */
    protected $status = 0;

    /** @Column(type="string") */
    protected $slug;

    /** @Column(type="string") */
    protected $path;

    /** @Column(type="string") */
    protected $link;

    /** @Column(type="string") */
    protected $title;

    /** @Column(type="string") */
    protected $type;

    /** @Column(type="string") */
    protected $menu = '';

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

    public function getParentId()
    {
        return $this->parentId;
    }

    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }

    public function getUrl($referenceType = false)
    {
        return App::url($this->getLink(), [], $referenceType);
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getMenu()
    {
        return $this->menu;
    }

    public function setMenu($menu)
    {
        $this->menu = $menu;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $node = $this->toJson();
        $node['url'] = $this->getUrl('base');
        return $node;
    }
}
