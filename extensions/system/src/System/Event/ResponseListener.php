<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\EventSubscriber;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ResponseListener extends EventSubscriber
{
    const REGEX_URL = '/
                        (href|src|poster)=        # match the attribute
                        ([\"\'])                  # start with a single or double quote
                        (?!\/|\#|[a-zA-Z0-9\s]+:) # make sure it is a relative path
                        ([^\"\'\s>]+)             # match the actual src value
                        \2                        # match the previous quote
                       /xiU';

    /**
     * Filter the response content.
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

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
        $route = '@' == $matches[3][0] ? $this['url']->route($matches[3]) : $this['url']->to($matches[3]);

        return "$matches[1]=\"$route\"";
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'kernel.response' => ['onKernelResponse', -10]
        ];
    }
}
