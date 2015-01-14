<?php

namespace Pagekit\Tree\Entity;

use Pagekit\Database\ORM\ModelTrait;
use Pagekit\Framework\Database\Event\EntityEvent;
use Pagekit\Tree\Model\NodeInterface;
use Pagekit\System\Entity\DataTrait;
use Pagekit\User\Entity\AccessTrait;

/**
 * @Entity(tableClass="@tree_node", eventPrefix="tree.node")
 */
class Node implements NodeInterface, \JsonSerializable
{
    use AccessTrait, DataTrait, ModelTrait;

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
    protected $title;

    /** @Column(type="string") */
    protected $type;

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

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
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
        $db = self::getConnection();

        $i  = 2;
        $id = $this->id;

        if (!$this->slug) {
            $this->slug = $this->getTitle();
        }

        while (self::where(['slug = ?', 'parent_id= ?'], [$this->slug, $this->parentId])->where(function ($query) use ($id) {
            if ($id) $query->where('id <> ?', [$id]);
        })->first()) {
            $this->slug = preg_replace('/-\d+$/', '', $this->slug).'-'.$i++;
        }

        // Update own path
        $path = '/'.$this->getSlug();
        if ($parent = self::find($this->getParentId())) {
            $path = $parent->getPath().$path;
        } else {
            // set Parent to 0, if old parent is not found
            $this->setParentId(0);
        }
        $this->setPath($path);

        if ($this->id) {
            // Update children's paths
            foreach (self::where('parent_id = ?', [$this->id])->get() as $child) {
                if (0 !== strpos($child->getPath(), $this->getPath().'/')) {
                    $child->save();
                }
            }
        } else {
            // Set priority
            $this->priority = 1 + $db->createQueryBuilder()
                    ->select($db->getDatabasePlatform()->getMaxExpression('priority'))
                    ->from('@tree_node')
                    ->where('parent_id = ?', [$this->parentId])
                    ->execute()->fetchColumn();
        }
    }
}
