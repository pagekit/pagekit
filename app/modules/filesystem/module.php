<?php

use Pagekit\Filesystem\Filesystem;
use Pagekit\Filesystem\Locator;
use Pagekit\Filesystem\StreamWrapper;
use Pagekit\Filesystem\Adapter\FileAdapter;

return [

    'name' => 'filesystem',

    'main' => function ($app) {

        $app['file'] = function () {
            return new Filesystem;
        };

        $app['locator'] = function () {
            return new Locator($this->config['path']);
        };

        $app->on('kernel.boot', function () use ($app) {
            StreamWrapper::setFilesystem($app['file']);
        });

        $app->on('kernel.request', function ($event) use ($app) {

            $request = $event->getRequest();
            $baseUrl = $request->getSchemeAndHttpHost().$request->getBaseUrl();

            $app['file']->registerAdapter('file', new FileAdapter($this->config['path'], $baseUrl));

        });

    },

    'autoload' => [

        'Pagekit\\Filesystem\\' => 'src'

    ],

    'config' => [

        'path' => getcwd()

    ]

];
