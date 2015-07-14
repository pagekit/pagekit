<?php

return [

    'name' => 'hello',

    'type' => 'extension',

    'main' => function() {
        // bootstrap code
    },

    'autoload' => [

        'Pagekit\\Hello\\' => 'src'

    ],

    'routes' => [

        '/' => [
            'name' => '@hello',
            'controller' => [
                'Pagekit\\Hello\\Controller\\HelloController',
                'Pagekit\\Hello\\Controller\\SiteController'
            ]
        ]

    ],

    'resources' => [

        'hello:' => ''

    ],

    'menu' => [

        'hello' => [
            'label' => 'Hello',
            'icon' => 'extensions/hello/extension.svg',
            'url' => '@hello',
            // 'access' => 'hello: manage hellos'
        ],

        'hello: index' => [
            'label' => 'Hello',
            'icon' => 'extensions/hello/extension.svg',
            'url' => '@hello',
            'parent' => 'hello'
            // 'access' => 'hello: manage hellos'
        ],

        'hello: settings' => [
            'label' => 'Settings',
            'url' => '@hello/settings',
            'parent' => 'hello',
            // 'access' => 'hello: manage hellos'
        ]

    ],

    'settings' => 'settings-hello',

    'config' => [

        'default' => 'World'

    ],

    'events' => [

        'enable.hello' => function () use ($app) {
            // run all migrations that are newer than the current version
            if ($version = $app['migrator']->create('hello:migrations', $this->config('version'))->run()) {
                $app['config']($this->name)->set('version', $version);
            }
        },

        'disable.hello' => function() use ($app) {
            // disable hook
        },

        'uninstall.hello' => function() use ($app) {
            // downgrade all migrations
            $app['migrator']->create('hello:migrations', $this->config('version'))->run(0);

            // remove the config
            $app['config']->remove($this->name);
        },

        'view.scripts' => function($event, $scripts) {
            $scripts->register('hello-settings', 'hello:app/bundle/settings.js', '~extensions');
        }

    ]

];
