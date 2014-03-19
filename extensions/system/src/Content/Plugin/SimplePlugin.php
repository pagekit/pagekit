<?php

namespace Pagekit\Content\Plugin;

use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\Content\Event\ContentEvent;
use Pagekit\System\Event\EditorLoadEvent;

class SimplePlugin extends EventSubscriber
{
    const PLUGIN_CODE = '/
                        \(([a-zA-Z_][a-zA-Z0-9_]*)\) # the plugin name
                        (\{                          # the optional options
                            (?:
                                ( [^\{\}] )+         # the options
                                |
                                (?-2)                # the bracket recursion
                            )*
                        \})?
                        /x';

    /**
     * Editor load callback.
     *
     * @param EditorLoadEvent $event
     */
    public function onEditorLoad(EditorLoadEvent $event) {}

    /**
     * Content plugins callback.
     *
     * @param ContentEvent $event
     */
    public function onContentPlugins(ContentEvent $event)
    {
        $content = preg_replace_callback(self::PLUGIN_CODE, function($matches) use ($event) {

            $options = isset($matches[2]) ? json_decode($matches[2], true) : array();

            if ($callback = $event->getPlugin($matches[1])) {
                return call_user_func($callback, (array) $options);
            }

        }, $event->getContent());

        $event->setContent($content);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'content.plugins' => array('onContentPlugins', 10)
        );
    }
}
