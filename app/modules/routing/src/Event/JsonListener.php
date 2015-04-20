<?php

namespace Pagekit\Routing\Event;

use Pagekit\Event\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class JsonListener implements EventSubscriberInterface
{
    /**
     * Transforms the body of a json request to POST parameters.
     *
     * @param $event
     */
    public function onRequest($event, $request)
    {
        if ('json' === $request->getContentType() && $data = json_decode($request->getContent(), true)) {
            $request->request->replace($data);
        }
    }

    /**
     * Handles responses in JSON format.
     *
     * @param $event
     */
    public function onController($event, $request)
    {
        $result = $event->getControllerResult();

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
            'app.request'    => ['onRequest', 130],
            'app.controller' => ['onController', 70]
        ];
    }
}
