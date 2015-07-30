<?php

namespace Pagekit\Comment\Event;

use Pagekit\Comment\Model\Comment;
use Pagekit\Comment\SpamDetection\SpamDetectionInterface;
use Pagekit\Event\EventSubscriberInterface;
use Psr\Log\LoggerInterface;

/**
 * A listener that checks if a comment is spam based on a service that implements SpamDetectionInterface.
 */
class SpamDetectionListener implements EventSubscriberInterface
{
    /**
     * @var SpamDetectionInterface
     */
    protected $detector;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param SpamDetectionInterface $detector
     * @param LoggerInterface        $logger
     */
    public function __construct(SpamDetectionInterface $detector, LoggerInterface $logger = null)
    {
        $this->detector = $detector;
        $this->logger   = $logger;
    }

    public function spamCheck(CommentEvent $event)
    {
        $comment = $event->getComment();

        if (!$this->detector->isSpam($comment)) {
            return;
        }

        if (null !== $this->logger) {
            $this->logger->info('Comment is marked as spam from detector.');
        }

        $comment->setStatus(Comment::STATUS_SPAM);

        $event->stopPropagation();
    }

    public function subscribe()
    {
        return ['system.comment.spam_check' => 'spamCheck'];
    }
}
