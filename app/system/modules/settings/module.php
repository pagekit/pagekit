<?php

return [

    'name' => 'system/settings',

    'main' => function ($app) {

        $mail  = $app['module']['mail'];
        $cache = $app['module']['cache'];

        // mail
        $app->on('system.settings.edit', function ($event) use ($app, $mail) {

            $app['view']->script('settings-mail', 'app/system/modules/settings/app/mail.js');

            $event->options($mail->name, $mail->config);
            $event->data('ssl', extension_loaded('openssl'));
            $event->section($mail->name, 'Mail', 'app/system/modules/settings/views/mail.php');
        });

        // cache
        $app->on('system.settings.edit', function ($event, $config) use ($app, $cache) {

            $app['view']->script('settings-cache', 'app/system/modules/settings/app/cache.js');

            $supported = $cache->supports();

            $caches = [
                'auto'   => ['name' => '', 'supported' => true],
                'apc'    => ['name' => 'APC', 'supported' => in_array('apc', $supported)],
                'xcache' => ['name' => 'XCache', 'supported' => in_array('xcache', $supported)],
                'file'   => ['name' => 'File', 'supported' => in_array('file', $supported)]
            ];

            $caches['auto']['name'] = 'Auto ('.$caches[end($supported)]['name'].')';

            $event->data('caches', $caches);
            $event->config($cache->name, $cache->config, ['caches.cache.storage', 'nocache']);
            $event->section($cache->name, 'Cache', 'app/system/modules/settings/views/cache.php');
        });

        $app->on('system.settings.save', function ($event, $config, $option) use ($app, $cache) {
            if ($config->get('cache.caches.cache.storage') != $cache->config('caches.cache.storage')) {
                $cache->clearCache();
            }
        });

    },

    'autoload' => [

        'Pagekit\\System\\' => 'src'

    ],

    'controllers' => [

        '@system: /system' => [
            'Pagekit\\System\\Controller\\CacheController',
            'Pagekit\\System\\Controller\\MailController',
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
