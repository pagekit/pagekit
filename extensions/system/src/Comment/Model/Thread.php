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
    protected $is_commentable = true;

    /**
     * @var integer
     */
    protected $num_comments = 0;

    /**
     * @var \DateTime
     */
    protected $last_comment_at = null;

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
        return $this->is_commentable;
    }

    /**
     * {@inheritdoc}
     */
    public function setCommentable($isCommentable)
    {
        $this->is_commentable = (bool) $isCommentable;
    }

    /**
     * {@inheritdoc}
     */
    public function getNumComments()
    {
        return $this->num_comments;
    }

    /**
     * {@inheritdoc}
     */
    public function setNumComments($numComments)
    {
        $this->num_comments = intval($numComments);
    }

    /**
     * {@inheritdoc}
     */
    public function incrementNumComments($by = 1)
    {
        return $this->num_comments += intval($by);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastCommentAt()
    {
        return $this->last_comment_at;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastCommentAt(\DateTime $lastCommentAt)
    {
        $this->last_comment_at = $lastCommentAt;
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
