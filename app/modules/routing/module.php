<?php

use Pagekit\Filter\FilterManager;
use Pagekit\Kernel\Exception\HttpException;
use Pagekit\Routing\Event\AliasListener;
use Pagekit\Routing\Event\ConfigureRouteListener;
use Pagekit\Routing\Event\RouterListener;
use Pagekit\Routing\Loader\RoutesLoader;
use Pagekit\Routing\Request\ParamFetcher;
use Pagekit\Routing\Request\ParamFetcherListener;
use Pagekit\Routing\Router;
use Pagekit\Routing\Routes;
use Symfony\Component\HttpFoundation\JsonResponse;

return [

    'name' => 'routing',

    'main' => function ($app) {

        $app['routes'] = function ($app) {
            return new Routes();
        };

        $app['router'] = function ($app) {
            return new Router($app['routes'], new RoutesLoader($app['events']), $app['request.stack'], ['cache' => $app['path.cache']]);
        };

    },

    'boot' => function ($app) {

        $app->subscribe(
            new ConfigureRouteListener,
            new ParamFetcherListener(new ParamFetcher(new FilterManager)),
            new RouterListener($app['router']),
            new AliasListener($app['routes'])
        );

        // $app['middleware'];

        $app->on('app.request', function () use ($app) {

            foreach ($app['module'] as $module) {

                if (!isset($module->routes)) {
                    continue;
                }

                foreach ($module->routes as $path => $route) {
                    $app['routes']->add(array_merge(['path' => $path], $route));
                }

            }

        }, 110);

        $app->on('app.controller', function ($event, $request) use ($app) {

            $name = $request->attributes->get('_route', '');

            if ($callback = $app['routes']->getCallback($name)) {
                $request->attributes->set('_controller', $callback);
            };

        }, 130);

        $app->error(function (HttpException $e) use ($app) {

            $request = $app['router']->getRequest();
            $types   = $request->getAcceptableContentTypes();

            if ('json' == $request->getFormat(array_shift($types))) {
                return new JsonResponse($e->getMessage(), $e->getCode());
            }

        }, -10);

    },

    'require' => [

        'kernel'

    ],

    'autoload' => [

        'Pagekit\\Routing\\' => 'src'

    ]

];
