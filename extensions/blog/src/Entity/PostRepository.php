<?php

namespace Pagekit\Blog\Entity;

use Pagekit\Comment\Model\CommentInterface;
use Pagekit\Component\Database\ORM\Repository;

class PostRepository extends Repository
{
    /**
     * {@inheritdoc}
     */
    public function updateCommentInfo($id)
    {
        $query = $this->manager->getRepository('Pagekit\Blog\Entity\Comment')->query()->where(array('thread_id' => $id, 'status' => CommentInterface::STATUS_VISIBLE));

        $this->where(compact('id'))->update(
            array(
                'num_comments' => $query->count(),
                'last_comment_at' => $query->max('created')
            )
        );
    }
}
