<?php

namespace Pagekit\Comment\Entity;

use Pagekit\Comment\Model\Comment as BaseComment;
use Pagekit\Comment\Model\CommentInterface;

abstract class Comment extends BaseComment
{
    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column(type="text") */
    protected $content;

    /** @Column(type="string") */
    protected $author;

    /** @Column(type="integer") */
    protected $depth = 0;

    /** @Column(type="datetime") */
    protected $created;

    /** @Column(type="smallint") */
    protected $status;

    /** @Column(type="integer") */
    protected $parent_id;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTime;
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * @param mixed $parentId
     */
    public function setParentId($parentId)
    {
        $this->parent_id = $parentId;
    }

    /**
     * {@inheritDoc}
     */
    public function setParent(CommentInterface $parent)
    {
        parent::setParent($parent);
        $this->setParentId($parent->getId());
    }
}
