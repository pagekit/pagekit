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
    public function onController($event)
    {
        $result = $event->getControllerResult();

        if (!(null === $result || is_array($result) || $result instanceof Response || (is_object($result) && !method_exists($result, '__toString')))) {
            $event->setResponse(new Response((string) $result));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'controller' => ['onController', 10],
        ];
    }
}
