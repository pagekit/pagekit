<?php

namespace Pagekit\Comment\Model;

abstract class Thread implements ThreadInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var CommentInterface[]
     */
    protected $comments;

    /**
     * @var bool
     */
    protected $isCommentable = true;

    /**
     * @var integer
     */
    protected $numComments = 0;

    /**
     * @var \DateTime
     */
    protected $lastCommentAt = null;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function isCommentable()
    {
        return $this->isCommentable;
    }

    /**
     * {@inheritdoc}
     */
    public function setCommentable($isCommentable)
    {
        $this->isCommentable = (bool) $isCommentable;
    }

    /**
     * {@inheritdoc}
     */
    public function getNumComments()
    {
        return $this->numComments;
    }

    /**
     * {@inheritdoc}
     */
    public function setNumComments($numComments)
    {
        $this->numComments = intval($numComments);
    }

    /**
     * {@inheritdoc}
     */
    public function incrementNumComments($by = 1)
    {
        return $this->numComments += intval($by);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastCommentAt()
    {
        return $this->lastCommentAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastCommentAt(\DateTime $lastCommentAt)
    {
        $this->lastCommentAt = $lastCommentAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setComments(array $comments = array())
    {
        $this->comments = $comments;
    }

    /**
     * {@inheritdoc}
     */
    public function getComments()
    {
        return $this->comments;
    }

    public function __toString()
    {
        return 'Comment thread #'.$this->getId();
    }
}
