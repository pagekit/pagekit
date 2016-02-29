<?php

namespace Pagekit\View\Event;

use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;

class ResponseListener implements EventSubscriberInterface
{
    const REGEX_URL = '/
                        \s                              # match a space
                        (?<attr>href|src|poster)=       # match the attribute
                        (?<quote>[\"\'])                # start with a single or double quote
                        (?!\/|\#|[a-z0-9\-\.]+\:)       # make sure it is a relative path
                        (?<url>[^\"\'>]+)               # match the actual src value
                        \2                              # match the previous quote
                       /xiU';

    const REGEX_BODY = '/<body(?<attributes>[^>]*)>(?<body>.*)<\/body>/xsi';

    /**
     * Filter the response content.
     */
    public function onResponse($event, $request, $response)
    {
        if (!is_string($content = $response->getContent())) {
            return;
        }

        $response->setContent(preg_replace_callback(self::REGEX_BODY, function($matches) {

            $body = preg_replace_callback(self::REGEX_URL, function ($matches) {
                return sprintf(' %s=%s%s%s', $matches['attr'], $matches['quote'], App::url($matches['url']), $matches['quote']);
            }, $matches['body']);

            return sprintf('<body%s>%s</body>', $matches['attributes'], $body);

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
