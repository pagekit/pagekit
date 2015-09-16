<?php

return [

    'name' => 'system/finder',

    'autoload' => [

        'Pagekit\\Finder\\' => 'src'

    ],

    'routes' => [

        '/system/finder' => [
            'name' => '@system/finder',
            'controller' => 'Pagekit\\Finder\\Controller\\FinderController'
        ],
        '/site/storage' => [
            'name' => '@site/storage',
            'controller' => 'Pagekit\\Finder\\Controller\\StorageController'
        ]

    ],

    'resources' => [

        'system/finder:' => ''

    ],

    'events' => [

        'boot' => function ($event, $app) {
            $this->config['storage'] = '/'.trim(($this->config['storage'] ?: 'storage'), '/');
            $app['path.storage']     = $app['path'].$this->config['storage'];
        },

        'view.scripts' => function ($event, $scripts) {
            $scripts->register('panel-finder', 'system/finder:app/bundle/panel-finder.js', ['vue', 'uikit-upload']);
            $scripts->register('input-image', 'system/finder:app/bundle/input-image.js', ['vue', 'panel-finder']);
            $scripts->register('input-video', 'system/finder:app/bundle/input-video.js', ['vue', 'panel-finder']);
        },

        'view.system:modules/settings/views/settings' => function ($event, $view) use ($app) {
            $view->data('$settings', ['config' => [$this->name => $this->config]]);
        },

        'system.finder' => function ($event) use ($app) {
            if ($app['user']->hasAccess('system: manage storage | system: manage storage read only')) {
                $event->path('#^'.preg_quote(strtr($app['path.storage'], '\\', '/'), '#').'($|\/.*)#', $app['user']->hasAccess('system: manage storage') ? 'w' : 'r');
            }
        }

    ],

    'permissions' => [

        'system: manage storage' => [
            'title' => 'Manage storage',
            'trusted' => true
        ],
        'system: manage storage read only' => [
            'title' => 'Manage storage (Read only)'
        ]

    ],

    'menu' => [

        'system: storage' => [
            'label' => 'Storage',
            'parent' => 'site',
            'url' => '@site/storage',
            'access' => 'system: manage storage',
            'priority' => 20
        ]

    ],

    'config' => [

        'storage' => '/storage'

    ]

];
