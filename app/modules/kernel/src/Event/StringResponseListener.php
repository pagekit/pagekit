<?php

namespace Pagekit\Kernel\Event;

use Pagekit\Event\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;

class StringResponseListener implements EventSubscriberInterface
{
    /**
     * Handles string responses.
     *
     * @param $event
     */
    public function onResponse($event)
    {
        $response = $event->getResponse();

        if (!(null === $response || is_array($response) || $response instanceof Response || (is_object($response) && !method_exists($response, '__toString')))) {
            $event->setResponse(new Response((string) $response));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'kernel.response' => ['onResponse', 10],
        ];
    }
}
