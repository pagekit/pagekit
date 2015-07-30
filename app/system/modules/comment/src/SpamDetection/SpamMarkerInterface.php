<?php

namespace Pagekit\Comment\SpamDetection;

use Pagekit\Comment\Model\Comment;

interface SpamMarkerInterface
{
    /**
     * Submits a comment as ham
     *
     * @param \Pagekit\Comment\Model\Comment $comment
     */
    public function markHam(Comment $comment);

    /**
     * Submits a comment as spam
     *
     * @param \Pagekit\Comment\Model\Comment $comment
     */
    public function markSpam(Comment $comment);
}
