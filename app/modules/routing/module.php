<?php

use Pagekit\Filter\FilterManager;
use Pagekit\Routing\Controller\AliasCollection;
use Pagekit\Routing\Controller\CallbackCollection;
use Pagekit\Routing\Controller\ControllerCollection;
use Pagekit\Routing\Controller\ControllerReader;
use Pagekit\Routing\Event\ConfigureRouteListener;
use Pagekit\Routing\Event\EventDispatcher;
use Pagekit\Routing\Event\JsonListener;
use Pagekit\Routing\Event\StringResponseListener;
use Pagekit\Routing\Middleware;
use Pagekit\Routing\Request\Event\ParamFetcherListener;
use Pagekit\Routing\Request\ParamFetcher;
use Pagekit\Routing\Router;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return [

    'name' => 'routing',

    'main' => function ($app) {

        $app['router'] = function ($app) {
            return new Router($app['kernel.events'], $app['request.stack'], ['cache' => $app['path.cache']]);
        };

        $app['aliases'] = function () {
            return new AliasCollection();
        };

        $app['callbacks'] = function () {
            return new CallbackCollection();
        };

        $app['controllers'] = function ($app) {
            return new ControllerCollection(new ControllerReader($app['kernel.events']), $app['autoloader'], $app['debug']);
        };

        $app['middleware'] = function($app) {
            return new Middleware($app['kernel.events']);
        };

        $app->on('kernel.boot', function () use ($app) {

            $events = $app['kernel.events'];
            $events->addSubscriber(new ConfigureRouteListener);
            $events->addSubscriber(new ParamFetcherListener(new ParamFetcher(new FilterManager)));
            $events->addSubscriber(new RouterListener($app['router'], null, null, $app['request.stack']));
            $events->addSubscriber(new ResponseListener('UTF-8'));
            $events->addSubscriber(new JsonListener);
            $events->addSubscriber(new StringResponseListener);
            $events->addSubscriber($app['aliases']);
            $events->addSubscriber($app['callbacks']);
            $events->addSubscriber($app['controllers']);

            $app['middleware'];

        });

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

        }, 35);

        $app->error(function (HttpExceptionInterface $e) use ($app) {

            $request = $app['router']->getRequest();
            $types   = $request->getAcceptableContentTypes();

            if ('json' == $request->getFormat(array_shift($types))) {
                return new JsonResponse($e->getMessage(), $e->getStatusCode());
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
