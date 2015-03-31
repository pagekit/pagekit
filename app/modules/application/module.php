<?php

use Pagekit\Application\Response;
use Pagekit\Application\UrlProvider;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

return [

    'name' => 'application',

    'main' => function ($app) {

        $debug   = (bool) $this->config('debug');
        $handler = ExceptionHandler::register($debug);

        ErrorHandler::register(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR);

        if ($cli = $app->runningInConsole() or $debug) {
            ini_set('display_errors', 1);
        }

        $app['exception'] = $handler;

        $app['url'] = function($app) {
            return new UrlProvider($app['router'], $app['file'], $app['locator']);
        };

        $app['response'] = function($app) {
            return new Response($app['url']);
        };

        // redirect the request if it has a trailing slash
        if (!$app->runningInConsole()) {

            $app->on('kernel.request', function(GetResponseEvent $event) {

                $path = $event->getRequest()->getPathInfo();

                if ('/' != $path && '/' == substr($path, -1) && '//' != substr($path, -2)) {
                    $event->setResponse(new RedirectResponse(rtrim($event->getRequest()->getUriForPath($path), '/'), 301));
                }

            }, 200);

        }
    },

    'require' => [

        'auth',
        'cookie',
        'database',
        'filesystem',
        'filter',
        'routing',
        'session',
        'view'

    ],

    'config' => [

        'debug' => true

    ]

];
