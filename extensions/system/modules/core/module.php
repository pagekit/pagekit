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

            $app['sections']->register('head', ['renderer' => 'delayed']);
            $app['sections']->prepend('head', function () use ($app) {
                return sprintf('        <meta name="generator" content="Pagekit %1$s">', $app['version']);
            });

            $app['exports']->get('pagekit')->add(['version' => $app['version'], 'url' => $app['router']->getContext()->getBaseUrl(), 'csrf' => $app['csrf']->generate()]);

            $app['isAdmin'] = (bool) preg_match('#^/admin(/?$|/.+)#', $request->getPathInfo());

            $app->trigger('system.init', $event);

        }, 50);
    },

    'require' => [

        'framework',
        'system/cache',
        'system/locale',
        'system/option',
        'system/templating',
        'system/view'

    ],

    'autoload' => [

        'Pagekit\\Content\\' => 'src'

    ],

    'config' => [

        'extensions' => [],
        'version' => '0.8.8'

    ]

];
