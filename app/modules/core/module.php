<?php

use Pagekit\Filesystem\Adapter\FileAdapter;
use Pagekit\Filesystem\Adapter\StreamAdapter;
use Symfony\Component\Finder\Finder;

return [

    'name' => 'system/core',

    'main' => function ($app) {

        $app['version'] = function() {
            return $this->config['version'];
        };

        $app->factory('finder', function() {
            return Finder::create();
        });

        $app->on('kernel.boot', function() use ($app) {

            $app['module']->load($this->config['extensions']);

            if ($app->runningInConsole()) {
                $app['isAdmin'] = false;
                $app->trigger('system.init');
            }

        });

        $app->on('kernel.request', function($event) use ($app) {

            if (!$event->isMasterRequest()) {
                return;
            }

            $request = $event->getRequest();
            $baseUrl = $request->getSchemeAndHttpHost().$request->getBaseUrl();

            $app['file']->registerAdapter('file', new FileAdapter($app['path'], $baseUrl));
            $app['file']->registerAdapter('app', new StreamAdapter($app['path'], $baseUrl));

            $app['view']->meta(['generator' => 'Pagekit '.$app['version']]);
            $app['view']->section()->register('head', ['renderer' => 'delayed']);

            $app['isAdmin'] = (bool) preg_match('#^/admin(/?$|/.+)#', $request->getPathInfo());

            $app->trigger('system.init', $event);

        }, 50);

        $app->on('kernel.request', function($event) use ($app) {

            if (!$event->isMasterRequest()) {
                return;
            }

            $app->trigger('system.loaded', $event);

        });

    },

    'require' => [

        'application',
        'cache',
        'option',
        'locale'

    ],

    'autoload' => [

        'Pagekit\\Content\\' => 'src'

    ],

    'config' => [

        'extensions' => [],
        'version' => '0.8.8'

    ]

];
