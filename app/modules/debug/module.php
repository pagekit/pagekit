<?php

use DebugBar\Storage\FileStorage;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\TimeDataCollector;
use Pagekit\Debug\DebugBar;
use Pagekit\Debug\DataCollector\AuthDataCollector;
use Pagekit\Debug\DataCollector\SystemDataCollector;
use Pagekit\Debug\Storage\SqliteStorage;

return [

    'name' => 'debug',

    'main' => function ($app) {

        if (!$this->config['enabled'] || !$this->config['file']) {
            return;
        }

        $app['debugbar'] = function ($app) {

            $debugbar = new DebugBar();
            $debugbar->setStorage($app['debugbar.storage']);
            $debugbar->addCollector(new AuthDataCollector($app['auth']));
            $debugbar->addCollector(new MemoryCollector());
            $debugbar->addCollector(new SystemDataCollector($app['info']));
            $debugbar->addCollector(new TimeDataCollector());

            return $debugbar;
        };

        $app['debugbar.storage'] = function () {
            return new SqliteStorage($this->config['file']);
        };

    },

    'boot' => function ($app) {

        if (!isset($app['debugbar'])) {
            return;
        }

        $app->subscribe($app['debugbar']);

        $app->on('kernel.request', function () use ($app) {
            $app['view']->data('$debugbar', ['url' => $app['router']->generate('_debugbar', ['id' => $app['debugbar']->getCurrentRequestId()])]);
            $app['view']->style('debugbar', 'app/modules/debug/assets/css/debugbar.css');
            $app['view']->script('debugbar', 'app/modules/debug/assets/app/debugbar.min.js', ['vue', 'jquery']);
        });

        $app['callbacks']->get('_debugbar/{id}', '_debugbar', function ($id) use ($app) {
            return $app['response']->json($app['debugbar']->getStorage()->get($id));
        })->setDefault('_debugbar', false);

    },

    'require' => [

        'view',
        'routing'

    ],

    'autoload' => [

        'Pagekit\\Debug\\' => 'src'

    ],

    'config' => [

        'file'    => null,
        'enabled' => false

    ]

];
