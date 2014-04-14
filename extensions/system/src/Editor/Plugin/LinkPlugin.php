<?php

namespace Pagekit\Editor\Plugin;

use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\System\Event\EditorLoadEvent;
use Pagekit\System\Event\TmplEvent;

class LinkPlugin extends EventSubscriber
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

        $event->addPlugin('link', 'extensions/system/assets/js/editor/link');
    }

    /**
     * Register Tmpls callback.
     *
     * @param TmplEvent $event
     */
    public function onSystemTmpl(TmplEvent $event)
    {
        $event->register('link.modal', 'extension://system/assets/tmpl/link.modal.razr.php');
        $event->register('link.replace', 'extension://system/assets/tmpl/link.replace.razr.php');
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
