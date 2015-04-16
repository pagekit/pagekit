<?php

namespace Pagekit\Routing\Event;

use Pagekit\Event\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class JsonListener implements EventSubscriberInterface
{
    /**
     * Transforms the body of a json request to POST parameters.
     *
     * @param $event
     */
    public function onKernelRequest($event)
    {
        $request = $event->getRequest();

        if ('json' === $request->getContentType() && $data = json_decode($request->getContent(), true)) {
            $request->request->replace($data);
        }
    }

    /**
     * Handles responses in JSON format.
     *
     * @param $event
     */
    public function onKernelView($event)
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
    public function subscribe()
    {
        return [
            'kernel.request' => 'onKernelRequest',
            'kernel.view'    => 'onKernelView'
        ];
    }
}
