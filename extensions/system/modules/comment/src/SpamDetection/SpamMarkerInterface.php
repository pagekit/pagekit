<?php

namespace Pagekit\Comment\SpamDetection;

use Pagekit\Comment\Model\CommentInterface;

/**
 * Spam marker interface.
 */
interface SpamMarkerInterface
{
    /**
     * Submits a comment as ham
     *
     * @param CommentInterface $comment
     */
    public function markHam(CommentInterface $comment);

    /**
     * Submits a comment as spam
     *
     * @param CommentInterface $comment
     */
    public function markSpam(CommentInterface $comment);
}
