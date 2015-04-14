<?php

namespace Pagekit\Content\Plugin;

use Pagekit\Content\Event\ContentEvent;
use Pagekit\Event\EventSubscriberInterface;

class SimplePlugin implements EventSubscriberInterface
{
    const PLUGIN_CODE = '/
                        \(([a-zA-Z_][a-zA-Z0-9_]*)\) # the plugin name
                        (\{                          # the plugin options
                            (?:
                                ( [^\{\}] )+
                                |
                                (?-2)                # the bracket recursion
                            )*
                        \})
                        /x';

    /**
     * Content plugins callback.
     *
     * @param ContentEvent $event
     */
    public function onContentPlugins(ContentEvent $event)
    {
        $content = preg_replace_callback(self::PLUGIN_CODE, function($matches) use ($event) {

            $options = isset($matches[2]) ? json_decode($matches[2], true) : [];

            if ($callback = $event->getPlugin($matches[1])) {
                return call_user_func($callback, (array) $options);
            }

        }, $event->getContent());

        $event->setContent($content);
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'content.plugins' => ['onContentPlugins', 10]
        ];
    }
}
