<?php

namespace Pagekit\Comment;

use Pagekit\Comment\Model\CommentNode;

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

    /**
     * Retrieves comments tree.
     *
     * @param  array $parameters
     * @return CommentNode
     */
    public function getCommentsTree(array $parameters = [])
    {
        $nodes = [new CommentNode(0)];

        foreach ($this->getComments() as $comment) {

            $id  = $comment->getId();
            $pid = $comment->getParentId();

            if (!isset($nodes[$id])) {
                $nodes[$id] = new CommentNode($id);
            }

            $nodes[$id]->setComment($comment);

            if (!isset($nodes[$pid])) {
                $nodes[$pid] = new CommentNode($pid);
            }

            $nodes[$pid]->add($nodes[$id]);
        }

        $root = $nodes[isset($parameters['root'], $nodes[$parameters['root']]) ? $parameters['root'] : 0];

        if (isset($parameters['order']) && $parameters['order'] == 'DESC') {
            $children = $root->getChildren();
            $root->removeAll();
            $root->addAll(array_reverse($children, true));
        }

        return $root;
    }
}
