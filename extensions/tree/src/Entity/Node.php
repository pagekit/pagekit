<?php

namespace Pagekit\Tree\Entity;

use Pagekit\Framework\Database\Event\EntityEvent;
use Pagekit\Tree\Model\NodeInterface;
use Pagekit\System\Entity\DataTrait;
use Pagekit\User\Entity\AccessTrait;

/**
 * @Entity(tableClass="@tree_node", eventPrefix="tree.node")
 */
class Node implements NodeInterface, \JsonSerializable
{
    use AccessTrait, DataTrait;

    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column(name="parent_id", type="integer") */
    protected $parentId = 0;

    /** @Column(type="integer") */
    protected $priority = 0;

    /** @Column(type="integer") */
    protected $status = 0;

    /** @Column(type="string") */
    protected $url = '';

    /** @Column(type="string") */
    protected $slug;

    /** @Column(type="string") */
    protected $path;

    /** @Column(type="string") */
    protected $mount = '';

    /** @Column(type="string") */
    protected $title;

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

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
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

    public function getMount()
    {
        return $this->mount;
    }

    public function setMount($mount)
    {
        $this->mount = $mount;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * @PreSave
     *
     * @param EntityEvent $event
     */
    public function preSave(EntityEvent $event)
    {
        $repository = $event->getEntityManager()->getRepository(get_class($this));

        $i  = 2;
        $id = $this->id;

        if (!$this->slug) {
            $this->slug = $this->getTitle();
        }

        while ($repository->query()->where(['slug = ?', 'parent_id= ?'], [$this->slug, $this->parentId])->where(function ($query) use ($id) {
                if ($id) $query->where('id <> ?', [$id]);
            })->first()) {
            $this->slug = preg_replace('/-\d+$/', '', $this->slug) . '-' . $i++;
        }

        // Update own path
        $path = '/' . $this->getSlug();
        if ($parent = $repository->find($this->getParentId())) {
            $path = $parent->getPath() . $path;
        } else {
            // set Parent to 0, if old parent is not found
            $this->setParentId(0);
        }
        $this->setPath($path);

        // Update childrens paths
        if ($this->id) {
            foreach ($repository->query()->where('parent_id = ?', [$this->id])->get() as $child) {
                if (0 !== strpos($child->getPath(), $this->getPath() . '/')) {
                    $repository->save($child);
                }
            }
        }
    }
}
