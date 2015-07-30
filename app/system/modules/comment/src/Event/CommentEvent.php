<?php

namespace Pagekit\Comment\Event;

use Symfony\Component\EventDispatcher\Event;

class CommentEvent extends Event
{
    /**
     * @var \Pagekit\Comment\Model\Comment
     */
    protected $comment;

    /**
     * Constructor.
     *
     * @param \Pagekit\Comment\Model\Comment $comment
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return \Pagekit\Comment\Model\Comment
     */
    public function getComment()
    {
        return $this->comment;
    }
}
