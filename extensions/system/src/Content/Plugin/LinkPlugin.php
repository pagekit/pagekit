<?php

namespace Pagekit\Content\Plugin;

use Pagekit\Content\Event\ContentEvent;
use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\System\Event\EditorLoadEvent;
use Pagekit\System\Event\TmplEvent;

class LinkPlugin extends EventSubscriber
{
    const LINK_CODE = '/
                            (href|src|poster)=   # match the attribute
                            ([\"\']?)           # optionally start with a single or double quote
                            (?!\/|[a-zA-Z0-9]+:) # make sure it is a relative path
                            ([^\"\'\s>]+?)       # match the actual src value
                            \2                   # match the previous quote
                        /xiU';

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
     * Content plugins callback.
     *
     * @param ContentEvent $event
     */
    public function onContentPlugins(ContentEvent $event)
    {
        $url = $this('url');

        $content = preg_replace_callback(self::LINK_CODE, function($matches) use ($url) {

            $route = '@' == $matches[3][0] ? $url->route($matches[3]) : $url->to($matches[3]);

            return "$matches[1]=\"$route\"";

        }, $event->getContent());

        $event->setContent($content);
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
            'editor.load'     => 'onEditorLoad',
            'content.plugins' => 'onContentPlugins',
            'system.tmpl'     => 'onSystemTmpl'
        );
    }
}
