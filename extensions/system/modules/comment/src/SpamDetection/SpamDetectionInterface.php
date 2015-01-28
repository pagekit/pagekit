<?php

namespace Pagekit\Comment\SpamDetection;

use Pagekit\Comment\Model\CommentInterface;

/**
 * Spam detection interface.
 */
interface SpamDetectionInterface
{
    /**
     * Takes the comment instance and should return a boolean value depending on whether the Spam service thinks the comment is spam.
     *
     * @param  CommentInterface $comment
     * @return boolean
     */
    public function isSpam(CommentInterface $comment);
}
