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
            'model.comment.saved' => 'onCommentChange',
            'model.comment.deleted' => 'onCommentChange'
        ];
    }
}
