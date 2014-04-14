<?php

namespace Pagekit\Content\Plugin;

use Pagekit\Content\Event\ContentEvent;
use Pagekit\Framework\Event\EventSubscriber;

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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'content.plugins' => 'onContentPlugins',
        );
    }
}
