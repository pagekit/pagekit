<?php

use Pagekit\Filesystem\Adapter\FileAdapter;
use Pagekit\Filesystem\Adapter\StreamAdapter;
use Symfony\Component\Finder\Finder;

return [

    'name' => 'system/core',

    'main' => function ($app) {

        $app->factory('finder', function() {
            return Finder::create();
        });

        $app['config']['app.storage'] = ltrim(($app['config']['app.storage'] ?: 'storage'), '/');
        $app['path.storage']          = $app['config']['locator.paths.storage'] = rtrim($app['path'].'/'.$app['config']['app.storage'], '/');

        $app->on('kernel.boot', function() use ($app) {

            $app['module']->load($this->config['extensions']);

            if ($app->runningInConsole()) {
                $app['isAdmin'] = false;
                $app->trigger('system.init');
            }

        });

        $app->on('kernel.request', function($event, $name, $dispatcher) use ($app) {

            if (!$event->isMasterRequest()) {
                return;
            }

            $request = $event->getRequest();
            $baseUrl = $request->getSchemeAndHttpHost().$request->getBaseUrl();
            $app['file']->registerAdapter('file', new FileAdapter($app['path'], $baseUrl));
            $app['file']->registerAdapter('app', new StreamAdapter($app['path'], $baseUrl));

            $app['sections']->register('head', ['renderer' => 'delayed']);
            $app['sections']->prepend('head', function () use ($app) {
                return sprintf('        <meta name="generator" content="Pagekit %1$s" data-version="%1$s" data-url="%2$s" data-csrf="%3$s">', $app['config']['app.version'], $app['router']->getContext()->getBaseUrl(), $app['csrf']->generate());
            });

            $app['isAdmin'] = (bool) preg_match('#^/admin(/?$|/.+)#', $request->getPathInfo());

            $dispatcher->dispatch('system.init', $event);

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

        'extensions' => []

    ]

];
