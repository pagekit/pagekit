<?php

namespace Pagekit\Comment\Event;

use Pagekit\Comment\Event\CommentEvent;
use Pagekit\Comment\Model\CommentEvents;
use Pagekit\Comment\Model\CommentInterface;
use Pagekit\Comment\SpamDetection\SpamMarkerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * A listener that tells spam markers to mark comments as spam or ham
 */
class SpamMarkerListener implements EventSubscriberInterface
{
    /**
     * @var SpamMarkerInterface
     */
    protected $spamMarker;

    /**
     * Constructor.
     *
     * @param SpamMarkerInterface  $marker
     */
    public function __construct(SpamMarkerInterface $marker)
    {
        $this->spamMarker = $marker;
    }

    public function mark(CommentEvent $event)
    {
        $comment = $event->getComment();

        if ($comment->getStatus() != $comment->getPreviousStatus()) {
            if ($comment->getStatus() == CommentInterface::STATUS_SPAM) {
                $this->spamMarker->markSpam($comment);
            } elseif ($comment->getPreviousStatus() == CommentInterface::STATUS_SPAM) {
                $this->spamMarker->markHam($comment);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(CommentEvents::POST_PERSIST => 'mark');
    }
}