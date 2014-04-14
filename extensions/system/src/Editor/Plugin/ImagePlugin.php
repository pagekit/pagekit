<?php

namespace Pagekit\Editor\Plugin;

use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\System\Event\EditorLoadEvent;
use Pagekit\System\Event\TmplEvent;

class ImagePlugin extends EventSubscriber
{
    /**
     * Editor load callback.
     *
     * @param EditorLoadEvent $event
     */
    public function onEditorLoad(EditorLoadEvent $event)
    {
        if ('markdown' != $event->getEditor()) {
            return;
        }

        $event->addPlugin('image', 'extensions/system/assets/js/editor/image');
    }

    /**
     * Register Tmpls callback.
     *
     * @param TmplEvent $event
     */
    public function onSystemTmpl(TmplEvent $event)
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
            'editor.load' => 'onEditorLoad',
            'system.tmpl' => 'onSystemTmpl'
        );
    }
}
