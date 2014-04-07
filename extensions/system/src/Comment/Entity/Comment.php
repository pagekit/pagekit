<?php

namespace Pagekit\Comment\Entity;

use Pagekit\Comment\Model\Comment as AbstractComment;
use Pagekit\Comment\Model\CommentInterface;
use Pagekit\Comment\Model\ThreadInterface;

abstract class Comment extends AbstractComment
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

    /** @Column(type="integer") */
    protected $thread_id;

    public function __construct()
    {
        $this->created = new \DateTime;
    }

    public function getThreadId()
    {
        return $this->thread_id;
    }

    public function setThreadId($threadId)
    {
        $this->thread_id = $threadId;
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
    public function setThread(ThreadInterface $thread)
    {
        parent::setThread($thread);
        $this->setThreadId($thread->getId());
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
