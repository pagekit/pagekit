<?php

namespace Pagekit\System\Content;

use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\System\Event\ContentEvent;
use Pagekit\System\Event\EditorLoadEvent;
use Pagekit\System\Event\RegisterTmplEvent;

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

        $event->addPlugin('image', 'extensions/system/assets/js/editor/image');
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
     * Content plugins callback.
     *
     * @param ContentEvent $event
     */
    public function onContentPlugins(ContentEvent $event)
    {
        $content = $event->getContent();
        $content = $this('markdown')->parse($content);

        $event->setContent($content);
    }

    /**
     * Register Tmpls callback.
     *
     * @param RegisterTmplEvent $event
     */
    public function onRegisterTmpl(RegisterTmplEvent $event)
    {
        $event->register('image.modal', 'extension://system/assets/tmpl/image.modal.razr.php');
        $event->register('image.replace', 'extension://system/assets/tmpl/image.replace.razr.php');
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'editor.load'        => array('onEditorLoad', -5),
            'content.plugins'    => array('onContentPlugins', 5),
            'view.register.tmpl' => 'onRegisterTmpl'
        );
    }
}