<?php

namespace Pagekit\Comment\Event;

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
    protected $marker;

    /**
     * Constructor.
     *
     * @param SpamMarkerInterface $marker
     */
    public function __construct(SpamMarkerInterface $marker)
    {
        $this->marker = $marker;
    }

    public function mark(MarkSpamEvent $event)
    {
        $comment = $event->getComment();

        if ($comment->getStatus() == $event->getPreviousStatus()) {
            return;
        }

        if ($comment->getStatus() == CommentInterface::STATUS_SPAM) {
            $this->marker->markSpam($comment);
        } elseif ($event->getPreviousStatus() == CommentInterface::STATUS_SPAM) {
            $this->marker->markHam($comment);
        }
    }

    public static function getSubscribedEvents()
    {
        return ['system.comment.spam_mark' => 'mark'];
    }
}
