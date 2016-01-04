<?php

use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\TimeDataCollector;
use Pagekit\Debug\DataCollector\AuthDataCollector;
use Pagekit\Debug\DataCollector\DatabaseDataCollector;
use Pagekit\Debug\DataCollector\EventDataCollector;
use Pagekit\Debug\DataCollector\ProfileDataCollector;
use Pagekit\Debug\DataCollector\RoutesDataCollector;
use Pagekit\Debug\DataCollector\SystemDataCollector;
use Pagekit\Debug\DebugBar;
use Pagekit\Debug\Event\TraceableEventDispatcher;
use Pagekit\Debug\Storage\SqliteStorage;
use Symfony\Component\Stopwatch\Stopwatch;

return [

    'name' => 'debug',

    'main' => function ($app) {

        if (!$this->config['enabled'] || !$this->config['file']) {
            return;
        }

        $app['debugbar'] = function ($app) {
            $debugbar = new DebugBar();
            return $debugbar->setStorage($app['debugbar.storage']);
        };

        $app['debugbar.storage'] = function () {
            return new SqliteStorage($this->config['file']);
        };

        $app['debugbar.stopwatch'] = function() {
            return new Stopwatch();
        };

        $app->extend('events', function($dispatcher, $app) {
            return new TraceableEventDispatcher($dispatcher, $app['debugbar.stopwatch']);
        });

    },

    'events' => [

        'boot' => function ($event, $app) {

            if (!isset($app['debugbar'])) {
                return;
            }

            $app['debugbar']->addCollector(new MemoryCollector());
            $app['debugbar']->addCollector(new TimeDataCollector());
            $app['debugbar']->addCollector(new RoutesDataCollector($app['router'], $app['events'], $app['path.cache']));
            $app['debugbar']->addCollector(new EventDataCollector($app['events'], $app['path']));
            $app['debugbar']->addCollector(new ProfileDataCollector($app['debugbar.storage']));

            if (isset($app['auth'])) {
                $app['debugbar']->addCollector(new AuthDataCollector($app['auth']));
            }

            if (isset($app['info'])) {
                $app['debugbar']->addCollector(new SystemDataCollector($app['info']));
            }

            if (isset($app['db'])) {
                $app['db']->getConfiguration()->setSQLLogger($app['db.debug_stack']);
                $app['debugbar']->addCollector(new DatabaseDataCollector($app['db'], $app['db.debug_stack']));
            }

            if (isset($app['log.debug'])) {
                $app['debugbar']->addCollector($app['log.debug']);
            }

            $app->on('view.head', function ($event, $view) use ($app) {

                if ($app['request']->get('_disable_debugbar')) {
                    return;
                }

                $view->data('$debugbar', ['current' => $app['debugbar']->getCurrentRequestId()]);
                $view->style('debugbar', 'app/modules/debug/assets/css/debugbar.css');
                $view->script('debugbar', 'app/modules/debug/app/bundle/debugbar.js', ['vue']);
            }, 50);

            $app->on('terminate', function ($event, $request) use ($app) {

                $route = $request->attributes->get('_route');

                if (!$event->isMasterRequest() || $route == '_debugbar') {
                    return;
                }

                $app['debugbar']->collect();

            }, -1000);

            $app['routes']->add([
                'name' => '_debugbar',
                'path' => '_debugbar/{id}',
                'defaults' => ['_debugbar' => false],
                'controller' => function ($id) use ($app) {
                    return $app['response']->json($app['debugbar']->getStorage()->get($id));
                }
            ]);

        }

    ],

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
