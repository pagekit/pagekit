<?php

use Pagekit\Kernel\Controller\ControllerListener;
use Pagekit\Kernel\Controller\ControllerResolver;
use Pagekit\Kernel\Event\JsonResponseListener;
use Pagekit\Kernel\Event\ResponseListener;
use Pagekit\Kernel\Event\StringResponseListener;
use Pagekit\Kernel\HttpKernel;
use Symfony\Component\HttpFoundation\RequestStack;

return [

    'name' => 'kernel',

    'main' => function ($app) {

        $app['kernel'] = function ($app) {

            $app->subscribe(
                new ControllerListener($app['resolver']),
                new ResponseListener(),
                new JsonResponseListener(),
                new StringResponseListener()
            );

            return new HttpKernel($app['events'], $app['request.stack']);
        };

        $app['resolver'] = function () {
            return new ControllerResolver();
        };

        $app->factory('request', function ($app) {
            return $app['request.stack']->getCurrentRequest();
        });

        $app['request.stack'] = function () {
            return new RequestStack();
        };

    },

    'events' => [

        'request' => [function ($event, $request) use ($app) {

            if ($app->inConsole()) {
                return;
            }

            $path = $request->getPathInfo();

            // redirect the request if it has a trailing slash
            if ('/' != $path && '/' == substr($path, -1) && '//' != substr($path, -2)) {
                $event->setResponse($app->redirect(rtrim($request->getUriForPath($path), '/'), [], 301));
            }

        }, 200]

    ],

    'autoload' => [

        'Pagekit\\Kernel\\' => 'src'

    ]

];
