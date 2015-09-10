<?php

return [

    'name' => 'system/cache',

    'main' => 'Pagekit\\Cache\\CacheModule',

    'autoload' => [

        'Pagekit\\Cache\\' => 'src'

    ],

    'routes' => [

        '/system/cache' => [
            'name' => '@system/cache',
            'controller' => 'Pagekit\\Cache\\Controller\\CacheController'
        ]

    ],

    'config' => [

        'caches' => [],
        'nocache' => false

    ],

    'events' => [

        'view.system:modules/settings/views/settings' => function ($event, $view) use ($app) {

            $supported = $this->supports();

            $caches = [
                'auto'   => ['name' => '', 'supported' => true],
                'apc'    => ['name' => 'APC', 'supported' => in_array('apc', $supported)],
                'xcache' => ['name' => 'XCache', 'supported' => in_array('xcache', $supported)],
                'file'   => ['name' => 'File', 'supported' => in_array('file', $supported)]
            ];

            $caches['auto']['name'] = "Auto ({$caches[end($supported)]['name']})";

            $view->data('$caches', $caches);
            $view->data('$settings', ['config' => [$this->name => $this->config(['caches.cache.storage', 'nocache'])]]);
            $view->script('settings-cache', 'app/system/modules/cache/app/bundle/settings.js', 'settings');

        },

        'after@system/settings/save' => function () {
            $this->clearCache();
        }

    ]

];
