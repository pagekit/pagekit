<?php

use Pagekit\Kernel\EventDispatcher;
use Pagekit\Kernel\HttpKernel;
use Pagekit\Kernel\Controller\ControllerResolver;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;

return [

    'name' => 'kernel',

    'main' => function ($app) {

        $app['kernel'] = function ($app) {
            return new HttpKernel($app['events'], $app['resolver'], $app['request.stack']);
        };

        $app['resolver'] = function ($app) {
            return new ControllerResolver($app['events']);
        };

        $app['request'] = function ($app) {
            return $app['request.stack']->getCurrentRequest();
        };

        $app['request.stack'] = function () {
            return new RequestStack();
        };

        $app['request.context'] = function ($app) {
            return new RequestContext();
        };

        // redirect the request if it has a trailing slash
        if (!$app->inConsole()) {

            $app->on('kernel.request', function ($event, $request) {

                $path = $request->getPathInfo();

                if ('/' != $path && '/' == substr($path, -1) && '//' != substr($path, -2)) {
                    $event->setResponse(new RedirectResponse(rtrim($request->getUriForPath($path), '/'), 301));
                }

            }, 200);

        }
    },

    'autoload' => [

        'Pagekit\\Kernel\\' => 'src'

    ]

];
