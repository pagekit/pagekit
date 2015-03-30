<?php

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

        $app->on('kernel.boot', function () use ($app) {
            StreamWrapper::setFilesystem($app['file']);
        });

    },

    'autoload' => [

        'Pagekit\\Filesystem\\' => 'src'

    ],

    'config' => [

        'path' => getcwd()

    ]

];
