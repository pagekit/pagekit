<?php

namespace Pagekit\Editor;

use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\System\Event\EditorLoadEvent;

class MarkdownEditor extends EventSubscriber
{
    /**
     * Loads the editor.
     *
     * TODO: refactor finder options
     */
    public function onEditorLoad(EditorLoadEvent $event)
    {
        if ('markdown' != $event->getEditor()) {
            return;
        }

        $event->addAttributes(array('data-editor' => 'markdown', 'autocomplete' => 'off', 'style' => 'visibility:hidden; height:543px;'));

        $this('view.scripts')->queue(
            'editor.markdown', 'extension://system/assets/js/editor/markdown.js', 'requirejs',
            array(
                'data-plugins' => json_encode(array_values($event->getPlugins())),
                'data-finder'  => json_encode(array('root' => $this('config')->get('app.storage'), 'mode' => 'write', 'hash' => $this('finder')->getToken($this('config')->get('app.storage'), 'write')))
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'editor.load'     => array('onEditorLoad', -5)
        );
    }
}
