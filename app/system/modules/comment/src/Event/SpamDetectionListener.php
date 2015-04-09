<?php

namespace Pagekit\Comment\Event;

use Pagekit\Comment\Model\CommentInterface;
use Pagekit\Comment\SpamDetection\SpamDetectionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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

        $comment->setStatus(CommentInterface::STATUS_SPAM);

        $event->stopPropagation();
    }

    public static function getSubscribedEvents()
    {
        return ['system.comment.spam_check' => 'spamCheck'];
    }
}
