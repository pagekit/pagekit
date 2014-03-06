<?php

namespace Pagekit\System\Content\Plugin;

use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\System\Event\ContentEvent;
use Pagekit\System\Event\EditorLoadEvent;
use Pagekit\System\Event\RegisterTmplEvent;

class LinkPlugin extends EventSubscriber
{
    const LINK_CODE = '/
                            (<a \s[^>]*href=) # match everything up to the href value
                            ([\"\']??)        # optionally start with a single or double quote
                            ([^\"\'\s>]*?)    # match the actual href value
                            \2                # match the previous quote
                            ([^>]*>.*<\/a>)   # match everything after the href value
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
        $content = $event->getContent();
        $content = preg_replace_callback(self::LINK_CODE, array($this, 'replaceLink'), $content, PREG_SET_ORDER);

        $event->setContent($content);
    }

    /**
     * Replace link callback.
     *
     * @param  array $matches
     * @return string
     */
    public function replaceLink($matches)
    {
        return $matches[1].'"'.$this('url')->to($matches[3]).'"'.$matches[4];
    }

    /**
     * Register Tmpls callback.
     *
     * @param RegisterTmplEvent $event
     */
    public function onRegisterTmpl(RegisterTmplEvent $event)
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
            'editor.load'        => 'onEditorLoad',
            'content.plugins'    => 'onContentPlugins',
            'view.register.tmpl' => 'onRegisterTmpl'
        );
    }
}
