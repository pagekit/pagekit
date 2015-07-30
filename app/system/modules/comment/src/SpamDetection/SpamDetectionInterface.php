<?php

namespace Pagekit\Comment\SpamDetection;

use Pagekit\Comment\Model\Comment;

interface SpamDetectionInterface
{
    /**
     * Takes the comment instance and should return a boolean value depending on whether the Spam service thinks the comment is spam.
     *
     * @param  \Pagekit\Comment\Model\Comment $comment
     * @return boolean
     */
    public function isSpam(Comment $comment);
}
