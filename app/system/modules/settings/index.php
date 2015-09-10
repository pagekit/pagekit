<?php

return [

    'name' => 'system/settings',

    'autoload' => [

        'Pagekit\\System\\' => 'src'

    ],

    'routes' => [

        '/system/settings' => [
            'name' => '@system/settings',
            'controller' => 'Pagekit\\System\\Controller\\SettingsController'
        ]

    ],

    'resources' => [

        'settings:' => ''

    ],

    'permissions' => [

        'system: access settings' => [
            'title' => 'Access system settings',
            'trusted' => true
        ]

    ],

    'menu' => [

        'system: system' => [
            'label' => 'System',
            'icon' => 'settings:assets/images/icon-settings.svg',
            'access' => 'system: access settings',
            'url' => '@system/settings',
            'priority' => 120
        ],

        'system: settings' => [
            'label' => 'Settings',
            'parent' => 'system: system',
            'url' => '@system/settings',
        ]

    ],

    'events' => [

        'view.system:modules/settings/views/settings' => function ($event, $view) use ($app) {

            $view->data('$system', [
                'locales' => $app->module('system/intl')->getAvailableLanguages(),
                'sqlite' => class_exists('SQLite3') || (class_exists('PDO') && in_array('sqlite', \PDO::getAvailableDrivers(), true))
            ]);

            $view->data('$settings', [
                'options' => [
                    'system' => $app['system']->config(['site.', 'admin.'])
                ],
                'config' => [
                    'application' => $app->module('application')->config(['debug']),
                    'debug' => $app->module('debug')->config(['enabled'])
                ]
            ]);

        }

    ]

];
