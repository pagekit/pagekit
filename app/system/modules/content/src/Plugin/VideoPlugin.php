<?php

namespace Pagekit\Content\Plugin;

use Pagekit\Content\Event\ContentEvent;
use Pagekit\Event\EventSubscriberInterface;

class VideoPlugin implements EventSubscriberInterface
{
    const REGEX_YOUTUBE = '/(\/\/.*?youtube\.[a-z]+)\/watch\?v=([^&]+)&?(.*)/';
    const REGEX_YOUTUBE_SHORT = '/youtu\.be\/(.*)/';
    const REGEX_VIMEO = '/(\/\/.*?)vimeo\.[a-z]+\/([0-9]+).*?/';

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

        $src = $options['src'];
        unset($options['src']);

        $html = '<video class="uk-responsive-width" src="%s"';
        foreach ($options as $attr => $value) {
            if ($value) {
                $html .= ' ' . $attr . (is_bool($value) ? '' : '="' . $value . '"');
            }
        }
        $html .= '></video>';

        if (isset($options['width'])) {
            $width = $options['width'];
            unset($options['width']);
        } else {
            $width = '690';
        }

        if (isset($options['height'])) {
            $height = $options['height'];
            unset($options['height']);
        } else {
            $height = '390';
        }

        $options['wmode'] = 'transparent';
        if (preg_match(self::REGEX_YOUTUBE, $src, $matches)) {

            if (isset($options['loop']) && $options['loop']) {
                $options['playlist'] = $matches[2];
            }

            $query = http_build_query($options);
            $src = "$matches[1]/embed/$matches[2]" . ($matches[3] ? "?$matches[3]" . '&' . $query : '?' . $query);
            $html = '<iframe class="uk-responsive-width" src="%s" width="%s" height="%s"></iframe>';

        } elseif (preg_match(self::REGEX_YOUTUBE_SHORT, $src, $matches)) {

            if (isset($options['loop']) && $options['loop']) {
                $options['playlist'] = $matches[1];
            }

            $query = http_build_query($options);
            $src = '//www.youtube.com/embed/' . array_pop(explode('/', $matches[1])) . '?' . $query;
            $html = '<iframe class="uk-responsive-width" src="%s" width="%s" height="%s"></iframe>';

        } elseif (preg_match(self::REGEX_VIMEO, $src, $matches)) {

            $query = http_build_query($options);
            $src = "$matches[1]player.vimeo.com/video/$matches[2]?$query";
            $html = '<iframe class="uk-responsive-width" src="%s" width="%s" height="%s"></iframe>';

        }

        return sprintf($html, $src, $width, $height);
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'content.plugins' => ['onContentPlugins', 15],
        ];
    }
}
