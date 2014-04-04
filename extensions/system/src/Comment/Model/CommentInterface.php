<?php

namespace Pagekit\Comment\Model;

interface CommentInterface
{
    const STATUS_VISIBLE = 0;

    const STATUS_DELETED = 1;

    const STATUS_SPAM = 2;

    const STATUS_PENDING = 3;

    /**
     * @return mixed unique ID for this comment
     */
    public function getId();

    /**
     * @return string name of the comment author
     */
    public function getAuthor();

    /**
     * @return string
     */
    public function getContent();

    /**
     * @param string $content
     */
    public function setContent($content);

    /**
     * @return \DateTime
     */
    public function getCreated();

    /**
     * @return ThreadInterface
     */
    public function getThread();

    /**
     * @param ThreadInterface $thread
     */
    public function setThread(ThreadInterface $thread);

    /**
     * @return integer The current status of the comment
     */
    public function getStatus();

    /**
     * @param integer $status
     */
    public function setStatus($status);

    /**
     * @return CommentInterface
     */
    public function getParent();

    /**
     * @param CommentInterface $parent
     */
    public function setParent(CommentInterface $parent);
}
