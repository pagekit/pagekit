<?php

namespace Pagekit\View\Event;

use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;

class ResponseListener implements EventSubscriberInterface
{
    const REGEX_URL = '/
                        (?<attr>href|src|poster)=              # match the attribute
                        ([\"\'])                               # start with a single or double quote
                        (?!\/|\#|(mailto|news|(ht|f)tp(s?))\:) # make sure it is a relative path
                        (?<url>[^\"\'>]+)                      # match the actual src value
                        \2                                     # match the previous quote
                       /xiU';

    /**
     * Filter the response content.
     */
    public function onResponse($event, $request, $response)
    {
        if (strpos($response->headers->get('Content-Type'), 'html') !== false) {
            $content = preg_replace_callback(self::REGEX_URL, [$this, 'replaceUrlCallback'], $response->getContent());
            $response->setContent($content);
        }
    }

    /**
     * Replace internal/relative url callback.
     *
     * @param  array  $matches
     * @return string
     */
    public function replaceUrlCallback($matches)
    {
        return sprintf('%s="%s"', $matches['attr'], App::url($matches['url']));
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'response' => ['onResponse', -20]
        ];
    }
}
