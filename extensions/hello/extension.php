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

    'nodes' => [

        'hello' => [
            'name' => '@hello',
            'label' => 'Hello',
            'controller' => 'Pagekit\\Hello\\Controller\\SiteController',
            'protected' => true
        ]

    ],

    'routes' => [

        '/hello' => [
            'name' => '@hello/admin',
            'controller' => [
                'Pagekit\\Hello\\Controller\\HelloController'
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
            'url' => '@hello/admin',
            // 'access' => 'hello: manage hellos'
        ],

        'hello: panel' => [
            'label' => 'Hello',
            'icon' => 'extensions/hello/extension.svg',
            'url' => '@hello/admin',
            'parent' => 'hello'
            // 'access' => 'hello: manage hellos'
        ]

    ],

    'permissions' => [

        'hello: manage settings' => [
            'title' => 'Manage settings'
        ],

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
            $scripts->register('hello-site', 'hello:app/bundle/site.js', '~site-edit');
            $scripts->register('hello-link', 'hello:app/bundle/link.js', '~panel-link');
        }

    ]

];
