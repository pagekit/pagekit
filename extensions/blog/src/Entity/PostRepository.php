<?php

namespace Pagekit\Blog\Entity;

use Pagekit\Comment\Model\CommentInterface;
use Pagekit\Component\Database\ORM\Repository;

class PostRepository extends Repository
{
    /**
     * Updates the comments info on post.
     *
     * @param int $id
     */
    public function updateCommentInfo($id)
    {
        $query = $this->manager->getRepository('Pagekit\Blog\Entity\Comment')->query()->where(['post_id' => $id, 'status' => CommentInterface::STATUS_APPROVED]);

        $this->where(compact('id'))->update([
                'comment_count' => $query->count()
            ]
        );
    }
}
