<?php

use Pagekit\Application\Response;
use Pagekit\Application\UrlProvider;
use Pagekit\Kernel\ExceptionHandler;
use Symfony\Component\Debug\ErrorHandler;

return [

    'name' => 'application',

    'main' => function ($app) {

        $app['version'] = function () {
            return $this->config['version'];
        };

        $app['debug'] = function () {
            return (bool) $this->config['debug'];
        };

        $app['url'] = function ($app) {
            return new UrlProvider($app['router'], $app['file'], $app['locator']);
        };

        $app['response'] = function ($app) {
            return new Response($app['url']);
        };

        $app['exception'] = ExceptionHandler::register($app['debug']);

        ErrorHandler::register()->throwAt(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR);

        ini_set('display_errors', $app->inConsole() || $app['debug'] ? 1 : 0);

    },

    'require' => [

        'debug',
        'routing',
        'auth',
        'config',
        'cookie',
        'database',
        'filesystem',
        'log',
        'session',
        'view'

    ],

    'config' => [

        'version' => '',
        'debug' => false

    ]

];
