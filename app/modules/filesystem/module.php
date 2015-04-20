<?php

use Pagekit\Filesystem\Adapter\FileAdapter;
use Pagekit\Filesystem\Filesystem;
use Pagekit\Filesystem\Locator;
use Pagekit\Filesystem\StreamWrapper;

return [

    'name' => 'filesystem',

    'main' => function ($app) {

        $app['file'] = function () {
            return new Filesystem;
        };

        $app['locator'] = function () {
            return new Locator($this->config['path']);
        };

    },

    'boot' => function ($app) {

        $app->on('app.request', function ($event, $request) use ($app) {

            $baseUrl = $request->getSchemeAndHttpHost().$request->getBaseUrl();

            $app['file']->registerAdapter('file', new FileAdapter($this->config['path'], $baseUrl));

        }, 100);

        StreamWrapper::setFilesystem($app['file']);

    },

    'autoload' => [

        'Pagekit\\Filesystem\\' => 'src'

    ],

    'config' => [

        'path' => getcwd()

    ]

];
