<?php

namespace Pagekit\Blog\Event;

use Pagekit\Blog\Entity\Post;
use Pagekit\Component\Database\Event\EntityEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CommentListener implements EventSubscriberInterface
{
    public function onCommentChange(EntityEvent $event)
    {
        Post::updateCommentInfo($event->getEntity()->getPostId());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'blog.comment.postSave' => 'onCommentChange',
            'blog.comment.postDelete' => 'onCommentChange'
        ];
    }
}
