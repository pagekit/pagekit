<?php

namespace Pagekit\Content\Plugin;

use Pagekit\Content\Event\ContentEvent;
use Pagekit\Framework\Event\EventSubscriber;

class VideoPlugin extends EventSubscriber
{
    const REGEX_YOUTUBE       = '/(\/\/.*?youtube\.[a-z]+)\/watch\?v=([^&]+)&?(.*)/';
    const REGEX_YOUTUBE_SHORT = '/youtu\.be\/(.*)/';
    const REGEX_VIMEO         = '/(\/\/.*?)vimeo\.[a-z]+\/([0-9]+).*?/';

    /**
     * Content plugins callback.
     *
     * @param ContentEvent $event
     */
    public function onContentPlugins(ContentEvent $event)
    {
        $event->addPlugin('video', [$this, 'applyPlugin']);
    }

    /**
     * Defines the plugins callback.
     *
     * @param  array $options
     * @return string
     */
    public function applyPlugin(array $options)
    {
        if (!isset($options['src'])) {
            return;
        }

        $src  = $options['src'];
        $html = '<video class="uk-width-1-1" src="%s"></video>';

        if (preg_match(self::REGEX_YOUTUBE, $src, $matches)) {

            $src  = "$matches[1]/embed/$matches[2]".($matches[3] ? "?$matches[3]" : '');
            $html = '<iframe class="uk-width-1-1" src="%s" height="360"></iframe>';

        } elseif (preg_match(self::REGEX_YOUTUBE_SHORT, $src, $matches)) {

            $src  = '//www.youtube.com/embed/'.array_pop(explode('/', $matches[1]));
            $html = '<iframe class="uk-width-1-1" src="%s" height="360"></iframe>';

        } elseif (preg_match(self::REGEX_VIMEO, $src, $matches)) {

            $src  = "$matches[1]player.vimeo.com/video/$matches[2]";
            $html = '<iframe class="uk-width-1-1" src="%s" height="360"></iframe>';

        }

        return sprintf($html, $src);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'content.plugins' => ['onContentPlugins', 15],
        ];
    }
}
