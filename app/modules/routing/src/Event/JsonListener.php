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
    public function onResponse($event, $request)
    {
        $response = $event->getResponse();

        if (!$response instanceof Response && strtolower($request->attributes->get('_response[value]', '', true)) == 'json') {
            $event->setResponse(new JsonResponse($response));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'kernel.request'  => ['onRequest', 130],
            'kernel.response' => ['onResponse', 30]
        ];
    }
}
