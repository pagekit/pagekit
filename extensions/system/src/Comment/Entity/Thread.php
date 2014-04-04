<?php

namespace Pagekit\Comment\Entity;

use Pagekit\Comment\Model\Thread as AbstractThread;

class Thread extends AbstractThread
{
    /** @Column(name="is_commentable", type="boolean") */
    protected $isCommentable;

    /** @Column(name="num_comments", type="integer") */
    protected $numComments = 0;

    /** @Column(name="last_comment_at", type="datetime") */
    protected $lastCommentAt;
}
