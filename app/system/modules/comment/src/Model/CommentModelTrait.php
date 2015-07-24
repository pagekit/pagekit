<?php

namespace Pagekit\Comment\Model;

use Pagekit\Database\ORM\ModelTrait;

trait CommentModelTrait
{
    use ModelTrait;

    /**
     * @Deleting
     */
    public static function deleting($event, CommentInterface $comment)
    {
        self::where(['parent_id = :old_parent'], [':old_parent' => $comment->getId()])->update(['parent_id' => $comment->getParentId()]);
    }
}
