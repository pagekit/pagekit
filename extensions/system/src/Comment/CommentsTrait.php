<?php

namespace Pagekit\Comment;

use Pagekit\Comment\Model\CommentNode;

trait CommentsTrait
{
    /**
     * return boolean
     */
    public function isCommentable()
    {
        return $this->is_commentable;
    }

    /**
     * @param bool $isCommentable
     */
    public function setCommentable($isCommentable)
    {
        $this->is_commentable = (bool) $isCommentable;
    }

    /**
     * @return integer
     */
    public function getNumComments()
    {
        return $this->num_comments;
    }

    /**
     * @param integer $numComments
     */
    public function setNumComments($numComments)
    {
        $this->num_comments = intval($numComments);
    }

    /**
     * @param  integer $by The number of comments to increment by
     * @return integer The new comment total
     */
    public function incrementNumComments($by = 1)
    {
        return $this->num_comments += intval($by);
    }

    /**
     * @return \DateTime
     */
    public function getLastCommentAt()
    {
        return $this->last_comment_at;
    }

    /**
     * @param  \DateTime
     * @return null
     */
    public function setLastCommentAt(\DateTime $lastCommentAt)
    {
        $this->last_comment_at = $lastCommentAt;
    }

    /**
     * Gets the comments.
     *
     * @return array $comments
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Sets the comments.
     *
     * @param array $comments
     */
    public function setComments(array $comments = [])
    {
        $this->comments = $comments;
    }
}
