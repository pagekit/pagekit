<?php

namespace Pagekit\Tree\Event;

use Pagekit\Tree\Annotation\Route;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class JsonRequestListener implements EventSubscriberInterface
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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest'
        ];
    }
}
