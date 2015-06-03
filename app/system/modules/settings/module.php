<?php

return [

    'name' => 'system/settings',

    'main' => function ($app) {

        $app->on('view.system:modules/settings/views/settings', function ($event, $view) use ($app) {

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

            $view->data('$system', [
                'locales' => $locales,
                'timezones' => $timezones,
                'sqlite' => class_exists('SQLite3') || (class_exists('PDO') && in_array('sqlite', \PDO::getAvailableDrivers(), true))
            ]);

            $view->data('$settings', [
                'options' => [
                    'system' => $app['system']->config(['api.', 'site.', 'admin.', 'timezone', 'release_channel'])
                ],
                'config' => [
                    'system' => $app['system']->config(['storage']),
                    'application' => $app['module']->get('application')->config(['debug']),
                    'debug' => $app['module']->get('debug')->config(['enabled'])
                ]
            ]);

        });

    },

    'autoload' => [

        'Pagekit\\System\\' => 'src'

    ],

    'routes' => [

        '@system/settings' => [
            'path' => '/system/settings',
            'controller' => 'Pagekit\\System\\Controller\\SettingsController'
        ]

    ],

    'resources' => [

            'settings:' => ''

    ],

    'menu' => [

        'system: system' => [
            'label'    => 'System',
            'icon'     => 'settings:assets/images/icon-settings.svg',
            'url'      => '@system/settings',
            'priority' => 120
        ],

        'system: settings' => [
            'label'    => 'Settings',
            'parent'   => 'system: system',
            'url'      => '@system/settings',
        ]

    ]

];
