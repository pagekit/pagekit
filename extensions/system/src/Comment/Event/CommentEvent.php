<?php

namespace Pagekit\Comment\Event;

use Pagekit\Comment\Model\CommentInterface;
use Pagekit\Framework\Event\Event;

class CommentEvent extends Event
{
    /**
     * @var CommentInterface
     */
    protected $comment;

    /**
     * Constructor.
     *
     * @param CommentInterface $comment
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return CommentInterface
     */
    public function getComment()
    {
        return $this->comment;
    }
}
