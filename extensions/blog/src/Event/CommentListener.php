<?php

namespace Pagekit\Blog\Event;

use Pagekit\Blog\Entity\Post;
use Pagekit\Database\Event\EntityEvent;
use Pagekit\Event\EventSubscriberInterface;

class CommentListener implements EventSubscriberInterface
{
    public function onCommentChange(EntityEvent $event)
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
