<?php

namespace Pagekit\Comment\Model;

interface ThreadInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @param string
     */
    public function setId($id);

    /**
     * return boolean
     */
    public function isCommentable();

    /**
     * @param bool $isCommentable
     */
    public function setCommentable($isCommentable);

    /**
     * @return integer
     */
    public function getNumComments();

    /**
     * @param integer $numComments
     */
    public function setNumComments($numComments);

    /**
     * @param  integer $by The number of comments to increment by
     * @return integer The new comment total
     */
    public function incrementNumComments($by);

    /**
     * @return \DateTime
     */
    public function getLastCommentAt();

    /**
     * @param  \DateTime
     * @return null
     */
    public function setLastCommentAt(\DateTime $lastCommentAt);

    /**
     * @return CommentInterface[]
     */
    public function getComments();

    /**
     * @param CommentInterface[] $comments
     */
    public function setComments(array $comments = array());
}
