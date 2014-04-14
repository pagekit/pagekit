<?php

namespace Pagekit\Content\Plugin;

use Pagekit\Content\Event\ContentEvent;
use Pagekit\Framework\Event\EventSubscriber;

class MarkdownPlugin extends EventSubscriber
{
    /**
     * Content plugins callback.
     *
     * @param ContentEvent $event
     */
    public function onContentPlugins(ContentEvent $event)
    {
        if ($event['markdown'] != true) {
            return;
        }

        $content = $event->getContent();
        $content = $this('markdown')->parse($content);

        $event->setContent($content);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'content.plugins' => array('onContentPlugins', 5)
        );
    }
}
