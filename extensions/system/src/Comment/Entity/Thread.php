<?php

namespace Pagekit\Comment\Entity;

use Pagekit\Comment\Model\Thread as AbstractThread;

class Thread extends AbstractThread
{
    /** @Column(type="boolean") */
    protected $is_commentable;

    /** @Column(type="integer") */
    protected $num_comments = 0;

    /** @Column(type="datetime") */
    protected $last_comment_at;
}
