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
        if (strpos($response->headers->get('Content-Type'), 'html') === false) {
            return;
        }

        $response->setContent(preg_replace_callback(self::REGEX_URL, function ($matches) {
            return sprintf('%s="%s"', $matches['attr'], App::url($matches['url']));
        }, $response->getContent()));
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
