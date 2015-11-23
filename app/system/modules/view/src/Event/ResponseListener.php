<?php

namespace Pagekit\View\Event;

use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;

class ResponseListener implements EventSubscriberInterface
{
    const REGEX_URL = '/
                        \s                              # match a space
                        (?<attr>href|src|poster)=       # match the attribute
                        ([\"\'])                        # start with a single or double quote
                        (?!\/|\#|[a-z0-9\-\.]+\:)       # make sure it is a relative path
                        (?<url>[^\"\'>]+)               # match the actual src value
                        \2                              # match the previous quote
                       /xiU';

    /**
     * Filter the response content.
     */
    public function onResponse($event, $request, $response)
    {
        if (!is_string($content = $response->getContent())) {
            return;
        }

        $response->setContent(preg_replace_callback(self::REGEX_URL, function ($matches) {
            return sprintf(' %s="%s"', $matches['attr'], App::url($matches['url']));
        }, $content));
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
