<?php

namespace Pagekit\System\Event;

use Pagekit\Application as App;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

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
        return sprintf('%s="%s"', $matches['attr'], App::url($matches['url']));
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
