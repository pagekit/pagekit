<?php

namespace Pagekit\Content\Plugin;

use Pagekit\Application as App;
use Pagekit\Content\Event\ContentEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MarkdownPlugin implements EventSubscriberInterface
{
    /**
     * Content plugins callback.
     *
     * @param ContentEvent $event
     */
    public function onContentPlugins(ContentEvent $event)
    {
        if (!$event['markdown']) {
            return;
        }

        $content = $event->getContent();
        $content = App::markdown()->parse($content, is_array($event['markdown']) ? $event['markdown'] : []);

        $event->setContent($content);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'content.plugins' => ['onContentPlugins', 5]
        ];
    }
}
