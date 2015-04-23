<?php

return [

    'name' => 'system/settings',

    'main' => function ($app) {

        $app->on('system.settings.edit', function ($event, $config) use ($app) {

            $locales = [];

            foreach ($app['finder']->directories()->depth(0)->in('app/system/languages')->name('/^[a-z]{2}(_[A-Z]{2})?$/') as $dir) {
                $code = $dir->getFileName();

                list($lang, $country) = explode('_', $code);

                $locales[$code] = $app['intl']['language']->getName($lang).' - '.$app['intl']['territory']->getName($country);
            }

            ksort($locales);

            $timezones = [];

            foreach (\DateTimeZone::listIdentifiers() as $timezone) {

                $parts = explode('/', $timezone);

                if (count($parts) > 2) {
                    $region = $parts[0];
                    $name = $parts[1].' - '.$parts[2];
                } elseif (count($parts) > 1) {
                    $region = $parts[0];
                    $name = $parts[1];
                } else {
                    $region = 'Other';
                    $name = $parts[0];
                }

                $timezones[$region][$timezone] = str_replace('_', ' ', $name);
            }

            $event->data('locales', $locales);
            $event->data('timezones', $timezones);
            $event->data('config', $config, ['application.debug', 'debug.enabled', 'system.storage']);
            $event->data('sqlite', class_exists('SQLite3') || (class_exists('PDO') && in_array('sqlite', \PDO::getAvailableDrivers(), true)));

            $event->options('system', $app['system']->config, ['api.key', 'release_channel', 'site.', 'maintenance.']);
            $event->options('system/locale', $app['system']->config, ['timezone', 'locale', 'locale_admin']);

            $event->section('site',   'Site', 'app/system/modules/settings/views/site.php');
            $event->section('system', 'System', 'app/system/modules/settings/views/system.php');
            $event->section('system/locale', 'Localization', 'app/system/modules/settings/views/locale.php');

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
