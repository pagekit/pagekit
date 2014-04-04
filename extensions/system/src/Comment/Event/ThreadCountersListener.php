<?php

namespace Pagekit\Comment\EventListener;

use Pagekit\Comment\Event\CommentEvent;
use Pagekit\Comment\Model\CommentEvents;
use Pagekit\Comment\Model\CommentManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * A listener that updates thread counters when a new comment is made.
 */
class ThreadCountersListener implements EventSubscriberInterface
{
    /**
     * @var CommentManagerInterface
     */
    private $commentManager;

    /**
     * Constructor.
     *
     * @param CommentManagerInterface $commentManager
     */
    public function __construct(CommentManagerInterface $commentManager)
    {
        $this->commentManager = $commentManager;
    }

    /**
     * Increase the thread comments number
     *
     * @param CommentEvent $event
     */
    public function onCommentPersist(CommentEvent $event)
    {
        $comment = $event->getComment();

        if (!$this->commentManager->isNew($comment)) {
            return;
        }

        $thread = $comment->getThread();
        $thread->incrementNumComments(1);
        $thread->setLastCommentAt($comment->getCreated());
    }

    /**
     * Decreases the thread comments number
     *
     * @param CommentEvent $event
     */
    public function onCommentDelete(CommentEvent $event)
    {
        $comment = $event->getComment();

        $thread = $comment->getThread();
        $thread->setNumComments($thread->getNumComments() - 1);

        // TODO: check if LastCommentAt needs to be refreshed
    }

    public static function getSubscribedEvents()
    {
        return array(
            CommentEvents::PRE_PERSIST => 'onCommentPersist',
            CommentEvents::PRE_DELETE => 'onCommentDelete'
        );
    }
}