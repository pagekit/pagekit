<?php

use Pagekit\Filter\FilterManager;
use Pagekit\Kernel\Event\ResponseListener;
use Pagekit\Routing\Controller\AliasCollection;
use Pagekit\Routing\Controller\CallbackCollection;
use Pagekit\Routing\Controller\ControllerCollection;
use Pagekit\Routing\Controller\ControllerReader;
use Pagekit\Routing\Event\ConfigureRouteListener;
use Pagekit\Routing\Event\EventDispatcher;
use Pagekit\Routing\Event\JsonListener;
use Pagekit\Routing\Event\RouterListener;
use Pagekit\Routing\Event\StringResponseListener;
use Pagekit\Routing\Middleware;
use Pagekit\Routing\Request\ParamFetcher;
use Pagekit\Routing\Request\ParamFetcherListener;
use Pagekit\Routing\Router;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return [

    'name' => 'routing',

    'main' => function ($app) {

        $app['router'] = function ($app) {
            return new Router($app['events'], $app['request.stack'], ['cache' => $app['path.cache']]);
        };

        $app['aliases'] = function () {
            return new AliasCollection();
        };

        $app['callbacks'] = function () {
            return new CallbackCollection();
        };

        $app['controllers'] = function ($app) {
            return new ControllerCollection(new ControllerReader($app['events']), $app['autoloader'], $app['debug']);
        };

        $app['middleware'] = function ($app) {
            return new Middleware($app['events']);
        };

    },

    'boot' => function ($app) {

        $app->subscribe(
            new ConfigureRouteListener,
            new ParamFetcherListener(new ParamFetcher(new FilterManager)),
            new RouterListener($app['router']),
            new JsonListener,
            $app['aliases'],
            $app['callbacks'],
            $app['controllers']
        );

        // $app['middleware'];

        $app->on('kernel.request', function () use ($app) {

            foreach ($app['module'] as $module) {

                if (!isset($module->controllers)) {
                    continue;
                }

                foreach ($module->controllers as $prefix => $controller) {

                    $namespace = '';

                    if (strpos($prefix, ':') !== false) {
                        list($namespace, $prefix) = explode(':', $prefix);
                    }

                    $app['controllers']->mount($prefix, $controller, $namespace);
                }

            }

        }, 110);

        // $app->error(function (HttpExceptionInterface $e) use ($app) {

        //     $request = $app['router']->getRequest();
        //     $types   = $request->getAcceptableContentTypes();

        //     if ('json' == $request->getFormat(array_shift($types))) {
        //         return new JsonResponse($e->getMessage(), $e->getStatusCode());
        //     }

        // }, -10);

    },

    'require' => [

        'kernel'

    ],

    'autoload' => [

        'Pagekit\\Routing\\' => 'src'

    ]

];
