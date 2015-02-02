<?php

use Pagekit\Database\DataCollector\DatabaseDataCollector;
use Pagekit\Profiler\DataCollector\SystemDataCollector;
use Pagekit\Profiler\DataCollector\UserDataCollector;
use Pagekit\Profiler\Profiler;
use Pagekit\Profiler\ToolbarListener;
use Pagekit\Profiler\TraceableEventDispatcher;
use Pagekit\Profiler\TraceableView;
use Pagekit\Routing\DataCollector\RoutesDataCollector;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcherInterface;
use Symfony\Component\HttpKernel\DataCollector\EventDataCollector;
use Symfony\Component\HttpKernel\DataCollector\MemoryDataCollector;
use Symfony\Component\HttpKernel\DataCollector\RequestDataCollector;
use Symfony\Component\HttpKernel\DataCollector\TimeDataCollector;
use Symfony\Component\HttpKernel\EventListener\ProfilerListener;
use Symfony\Component\HttpKernel\Profiler\SqliteProfilerStorage;
use Symfony\Component\Stopwatch\Stopwatch;

return [

    'name' => 'system/profiler',

    'main' => function ($app, $config) {

        $this->app  = $app;
        $this->path = $config['path'];

        if (!$config['enabled'] || !$config['file']) {
            return;
        }

        if (!(class_exists('SQLite3') || (class_exists('PDO') && in_array('sqlite', \PDO::getAvailableDrivers(), true)))) {
            return;
        }

        $app['profiler'] = function($app) {

            $profiler = new Profiler($app['profiler.storage']);

            if ($app['events'] instanceof TraceableEventDispatcherInterface) {
                $app['events']->setProfiler($profiler);
            }

            return $profiler;
        };

        $app['profiler.storage'] = function() use ($config) {
            return new SqliteProfilerStorage('sqlite:'.$config['file'], '', '', 86400);
        };

        $app['profiler.stopwatch'] = function() {
            return new Stopwatch;
        };

        $app->extend('events', function($dispatcher, $app) {
            return new TraceableEventDispatcher($dispatcher, $app['profiler.stopwatch']);
        });

        $app->extend('view', function($view, $app) {
            return new TraceableView($view, $app['profiler.stopwatch']);
        });

        $app->on('kernel.boot', function() use ($app) {

            $toolbar = $this->path.'/views/toolbar/';
            $panel   = $this->path.'/views/panel/';

            $app['profiler']->add($request = new RequestDataCollector, "$toolbar/request.php", "$panel/request.php", 40);
            $app['profiler']->add(new RoutesDataCollector($app['router'], $app['path.cache']), "$toolbar/routes.php", "$panel/routes.php", 35);
            $app['profiler']->add(new TimeDataCollector, "$toolbar/time.php", "$panel/time.php", 20);
            $app['profiler']->add(new MemoryDataCollector, "$toolbar/memory.php");
            $app['profiler']->add(new EventDataCollector, "$toolbar/events.php", "$panel/events.php", 30);

            if (isset($app['db']) && isset($app['db.debug_stack'])) {
                $app['profiler']->add(new DatabaseDataCollector($app['db'], $app['db.debug_stack']), "$toolbar/db.php", "$panel/db.php", -10);
                $app['db']->getConfiguration()->setSQLLogger($app['db.debug_stack']);
            }

            $app->on('system.init', function() use ($app) {
                $app['profiler']->add(new SystemDataCollector($app['systemInfo']), 'extensions/system/modules/profiler/views/toolbar/system.php', 'extensions/system/modules/profiler/views/panel/system.php', 50);
                $app['profiler']->add(new UserDataCollector($app['auth']), 'extensions/system/modules/profiler/views/toolbar/user.php', null, -20);
            });

            $app->subscribe(new ProfilerListener($app['profiler']));
            $app->subscribe($request);
            $app->subscribe(new ToolbarListener);

        });
    },

    'autoload' => [

        'Pagekit\\Profiler\\' => 'src'

    ],

    'file'    => null,
    'enabled' => false

];
