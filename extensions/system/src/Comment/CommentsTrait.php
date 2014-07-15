<?php

namespace Pagekit\Comment;

trait CommentsTrait
{
    /**
     * Gets the comment status.
     *
     * @return bool $comment_status
     */
    public function getCommentStatus()
    {
        return $this->comment_status;
    }

    /**
     * Sets the comment status.
     *
     * @param bool $comment_status
     */
    public function setCommentStatus($comment_status)
    {
        $this->comment_status = (bool) $comment_status;
    }

    /**
     * Gets the comment count.
     *
     * @return int $comment_count
     */
    public function getCommentCount()
    {
        return $this->comment_count;
    }

    /**
     * Sets the comment count.
     *
     * @param int $comment_count
     */
    public function setCommentCount($comment_count)
    {
        $this->comment_count = (int) $comment_count;
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
