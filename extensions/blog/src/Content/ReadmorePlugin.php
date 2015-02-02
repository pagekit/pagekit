<?php

namespace Pagekit\Blog\Content;

use Pagekit\Content\Event\ContentEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReadmorePlugin implements EventSubscriberInterface
{
    /**
     * Content plugins callback.
     *
     * @param ContentEvent $event
     */
    public function onContentPlugins(ContentEvent $event)
    {
        $content = preg_split('/\[readmore\]/i', $event->getContent());

        if ($event['readmore'] && count($content) > 1) {
            $event['post']->readmore = true;
            $event->setContent($content[0]);
        } else {
            $event->setContent(implode('', $content));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'content.plugins' => ['onContentPlugins', 10]
        ];
    }
}
