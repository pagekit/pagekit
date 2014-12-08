<?php

namespace Pagekit\Tree\Event;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Pagekit\Component\Routing\Event\ConfigureRouteEvent;
use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\Tree\Annotation\Route;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class JsonRequestListener extends EventSubscriber
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
