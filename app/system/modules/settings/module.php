<?php

return [

    'name' => 'system/settings',

    'main' => function ($app) {

        $app->on('system.settings.edit', function ($event, $config) use ($app) {

            $event->options('system', $app['system']->config, ['api.key', 'release_channel', 'site.', 'maintenance.']);
            $event->data('config', $config, ['application.debug', 'debug.enabled', 'system.storage']);
            $event->data('sqlite', class_exists('SQLite3') || (class_exists('PDO') && in_array('sqlite', \PDO::getAvailableDrivers(), true)));
            $event->section('site',   'Site', 'app/system/modules/settings/views/site.php');
            $event->section('system', 'System', 'app/system/modules/settings/views/system.php');

        }, 8);

        $app->on('system.settings.save', function ($event, $config) use ($app) {

            if ($config['application.debug'] != $app['module']['application']->config('debug')) {
                $app['module']['system/cache']->clearCache();
            }

        });

    },

    'autoload' => [

        'Pagekit\\System\\' => 'src'

    ],

    'controllers' => [

        '@system: /system' => [
            'Pagekit\\System\\Controller\\SettingsController'
        ]

    ],

    'menu' => [

        'system: system' => [
            'label'    => 'System',
            'icon'     => 'app/system/assets/images/icon-settings.svg',
            'url'      => '@system/settings',
            'priority' => 110
        ],

        'system: settings' => [
            'label'    => 'Settings',
            'parent'   => 'system: system',
            'url'      => '@system/settings',
            'priority' => 120
        ]

    ]

];
