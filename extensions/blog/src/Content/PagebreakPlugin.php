<?php

namespace Pagekit\Blog\Content;

use Pagekit\Content\Event\ContentEvent;
use Pagekit\Framework\Event\EventSubscriber;

class PagebreakPlugin extends EventSubscriber
{
    /**
     * Content plugins callback.
     *
     * @param ContentEvent $event
     */
    public function onContentPlugins(ContentEvent $event)
    {
        $current = $this['request']->get('page', 1) - 1;
        $content = preg_split('/\[pagebreak\]/i', $event->getContent());

        if (isset($content[$current])) {
            $event->setContent($content[$current]);
        } else {
            $event->setContent($content[0]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'content.plugins' => 'onContentPlugins'
        ];
    }
}
