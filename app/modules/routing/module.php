<?php

use Pagekit\Filter\FilterManager;
use Pagekit\Routing\Controller\AliasCollection;
use Pagekit\Routing\Controller\CallbackCollection;
use Pagekit\Routing\Controller\ControllerCollection;
use Pagekit\Routing\Controller\ControllerReader;
use Pagekit\Routing\Controller\ControllerResolver;
use Pagekit\Routing\Event\ConfigureRouteListener;
use Pagekit\Routing\Event\JsonListener;
use Pagekit\Routing\Event\StringResponseListener;
use Pagekit\Routing\Request\Event\ParamFetcherListener;
use Pagekit\Routing\Request\ParamFetcher;
use Pagekit\Routing\Router;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\HttpKernel;

return [

    'name' => 'routing',

    'main' => function ($app) {

        $app['router'] = function($app) {
            return new Router($app['events'], $app['kernel'], ['cache' => $app['path.cache']]);
        };

        $app['kernel'] = function($app) {
            return new HttpKernel($app['events'], $app['resolver'], $app['request_stack']);
        };

        $app['request_stack'] = function () {
            return new RequestStack;
        };

        $app['resolver'] = function($app) {
            return new ControllerResolver($app['events']);
        };

        $app['aliases'] = function() {
            return new AliasCollection;
        };

        $app['callbacks'] = function() {
            return new CallbackCollection;
        };

        $app['controllers'] = function($app) {
            return new ControllerCollection(new ControllerReader($app['events']), $app['autoloader'], $app['debug']);
        };

        $app->on('kernel.boot', function() use ($app) {
            $app->subscribe(
                new ConfigureRouteListener,
                new ParamFetcherListener(new ParamFetcher(new FilterManager)),
                new RouterListener($app['router'], null, null, $app['request_stack']),
                new ResponseListener('UTF-8'),
                new JsonListener,
                new StringResponseListener,
                $app['aliases'],
                $app['callbacks'],
                $app['controllers']
            );
        });

        $app->on('kernel.request', function() use ($app) {

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

        $app->error(function(HttpExceptionInterface $e) use ($app) {

            $request = $app['router']->getRequest();
            $types   = $request->getAcceptableContentTypes();

            if ($request->isXmlHttpRequest() && 'json' == $request->getFormat(array_shift($types))) {
                return new JsonResponse($e->getMessage(), $e->getStatusCode());
            }

        });

    },

    'autoload' => [

        'Pagekit\\Routing\\' => 'src'

    ]

];
