<?php

use Pagekit\Filter\FilterManager;
use Pagekit\Kernel\Exception\HttpException;
use Pagekit\Routing\Event\AliasListener;
use Pagekit\Routing\Event\ConfigureRouteListener;
use Pagekit\Routing\Event\RouterListener;
use Pagekit\Routing\Loader\RoutesLoader;
use Pagekit\Routing\Middleware;
use Pagekit\Routing\Request\ParamFetcher;
use Pagekit\Routing\Request\ParamFetcherListener;
use Pagekit\Routing\Router;
use Pagekit\Routing\Routes;
use Symfony\Component\HttpFoundation\JsonResponse;

return [

    'name' => 'routing',

    'main' => function ($app) {

        $app['routes'] = function () {
            return new Routes();
        };

        $app['router'] = function ($app) {
            return new Router($app['routes'], new RoutesLoader($app['events']), $app['request.stack'], ['cache' => $app['path.cache']]);
        };

        $app['middleware'] = function ($app) {
            return new Middleware($app['events']);
        };

        $app['module']->addLoader(function ($module) use ($app) {

            if (isset($module['routes'])) {
                foreach ($module['routes'] as $path => $route) {
                    $app['routes']->add(array_merge(['path' => $path], $route));
                }
            }

            return $module;
        });

    },

    'events' => [

        'boot' => function ($event, $app) {

            $app->subscribe(
                new ConfigureRouteListener,
                new ParamFetcherListener(new ParamFetcher(new FilterManager)),
                new RouterListener($app['router']),
                new AliasListener($app['routes'])
            );

            $app['middleware'];

            $app->error(function (HttpException $e) use ($app) {

                $request = $app['router']->getRequest();
                $types   = $request->getAcceptableContentTypes();

                if ('json' == $request->getFormat(array_shift($types))) {
                    return new JsonResponse($e->getMessage(), $e->getCode());
                }

            }, -10);

        },

        'request' => [function ($event, $request) use ($app) {

            if ($redirect = $request->attributes->get('_redirect')) {
                $event->setResponse($app->redirect($redirect), [], 301);
            };

        }, 90],

        'controller' => [function ($event, $request) use ($app) {

            if (!$request->attributes->get('_controller') && $callback = $app['routes']->getCallback($request->attributes->get('_route', ''))) {
                $request->attributes->set('_controller', $callback);
            };

        }, 130]

    ],

    'require' => [

        'kernel',
        'filter'

    ],

    'autoload' => [

        'Pagekit\\Routing\\' => 'src'

    ]

];
