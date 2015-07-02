<?php

namespace Pagekit\Blog\Event;

use Pagekit\Blog\Model\Post;
use Pagekit\Event\EventSubscriberInterface;

class CommentListener implements EventSubscriberInterface
{
    public function onCommentChange($event)
    {
        Post::updateCommentInfo($event->getEntity()->getPostId());
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'blog.comment.postSave' => 'onCommentChange',
            'blog.comment.postDelete' => 'onCommentChange'
        ];
    }
}
