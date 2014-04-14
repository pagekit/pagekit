<?php

namespace Pagekit\Editor\Plugin;

use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\System\Event\EditorLoadEvent;
use Pagekit\System\Event\TmplEvent;

class VideoPlugin extends EventSubscriber
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

        $event->addPlugin('video', 'extensions/system/assets/js/editor/video');
    }

    /**
     * Register Tmpls callback.
     *
     * @param TmplEvent $event
     */
    public function onSystemTmpl(TmplEvent $event)
    {
        $event->register('video.modal', 'extension://system/assets/tmpl/video.modal.razr.php');
        $event->register('video.replace', 'extension://system/assets/tmpl/video.replace.razr.php');
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
