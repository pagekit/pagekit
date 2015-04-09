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

        $app['version'] = function () {
            return $this->config['version'];
        };

        $app['debug'] = function () {
            return $this->config['debug'];
        };

        $app['url'] = function ($app) {
            return new UrlProvider($app['router'], $app['file'], $app['locator']);
        };

        $app['response'] = function ($app) {
            return new Response($app['url']);
        };

        $app['exception'] = ExceptionHandler::register($app['debug']);

        ErrorHandler::register(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR);

        if ($app->inConsole() or $app['debug']) {
            ini_set('display_errors', 1);
        }

        // redirect the request if it has a trailing slash
        if (!$app->inConsole()) {

            $app->on('kernel.request', function (GetResponseEvent $event) {

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

        'version' => '',
        'debug'   => false

    ]

];
