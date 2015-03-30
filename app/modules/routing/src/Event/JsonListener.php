<?php

namespace Pagekit\Routing\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class JsonListener implements EventSubscriberInterface
{
    /**
     * Transforms the body of a json request to POST parameters.
     *
     * @param  GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ('json' === $request->getContentType() && $data = json_decode($request->getContent(), true)) {
            $request->request->replace($data);
        }
    }

    /**
     * Handles responses in JSON format.
     *
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        $result  = $event->getControllerResult();

        if (strtolower($request->attributes->get('_response[value]', '', true)) == 'json') {
            $event->setResponse(new JsonResponse($result));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::VIEW    => 'onKernelView'
        ];
    }
}
