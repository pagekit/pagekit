<?php

use Pagekit\Finder\FinderHelper;

return [

    'name' => 'system/finder',

    'main' => function ($app) {

        $this->config['storage'] = '/'.trim(($this->config['storage'] ?: 'storage'), '/');
        $app['path.storage'] = $app['path'].$this->config['storage'];

    },

    'autoload' => [

        'Pagekit\\Finder\\' => 'src'

    ],

    'routes' => [

        '/system/finder' => [
            'name' => '@system/finder',
            'controller' => 'Pagekit\\Finder\\Controller\\FinderController'
        ],
        '/system/storage' => [
            'name' => '@system/storage',
            'controller' => 'Pagekit\\Finder\\Controller\\StorageController'
        ]

    ],

    'resources' => [

        'system/finder:' => ''

    ],

    'events' => [

        'boot' => function($event, $app) {
            $this->config['storage'] = '/'.trim(($this->config['storage'] ?: 'storage'), '/');
            $app['path.storage'] = rtrim($app['path'].$this->config['storage'], '/');
        },

        'request' => function () use ($app) {

            $app['view']->addHelper(new FinderHelper());
            $app['scripts']->register('finder', 'system/finder:app/bundle/finder.js', ['vue', 'uikit-upload']);

        },

        'view.system:modules/settings/views/settings' => function ($event, $view) use ($app) {
            $view->data('$settings', ['config' => [$this->name => $this->config]]);
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
            'parent' => 'system: system',
            'url' => '@system/storage',
            'access' => 'system: manage storage',
            'priority' => 20
        ]

    ],

    'config' => [

        'storage' => '/storage'

    ]

];
