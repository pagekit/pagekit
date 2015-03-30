<?php

namespace Pagekit\Routing\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class StringResponseListener implements EventSubscriberInterface
{
    /**
     * Handles string responses.
     *
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $result = $event->getControllerResult();

        if (!(null === $result || is_array($result) || $result instanceof Response || (is_object($result) && !method_exists($result, '__toString')))) {
            $event->setResponse(new Response((string) $result));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['onKernelView', -10],
        ];
    }
}
