<?php

use Pagekit\Database\DataCollector\DatabaseDataCollector;
use Pagekit\Profiler\DataCollector\RequestDataCollector;
use Pagekit\Profiler\DataCollector\SystemDataCollector;
use Pagekit\Profiler\DataCollector\UserDataCollector;
use Pagekit\Profiler\Profiler;
use Pagekit\Profiler\TraceableView;
use Pagekit\Profiler\Event\ProfilerListener;
use Pagekit\Profiler\Event\ToolbarListener;
use Pagekit\Profiler\Event\TraceableEventDispatcher;
use Pagekit\Routing\DataCollector\RoutesDataCollector;
use Symfony\Component\HttpKernel\DataCollector\EventDataCollector;
use Symfony\Component\HttpKernel\DataCollector\MemoryDataCollector;
use Symfony\Component\HttpKernel\DataCollector\TimeDataCollector;
use Symfony\Component\HttpKernel\Profiler\SqliteProfilerStorage;
use Symfony\Component\Stopwatch\Stopwatch;

return [

    'name' => 'profiler',

    'main' => function ($app) {

        if (!($this->config['enabled']
            && $this->config['file']
            && class_exists('SQLite3')
            && class_exists('PDO')
            && in_array('sqlite', \PDO::getAvailableDrivers(), true)
        )) {
            return;
        }

        $app['profiler'] = function($app) {

            $profiler = new Profiler($app['profiler.storage']);

            if ($app['events'] instanceof TraceableEventDispatcher) {
                $app['events']->setProfiler($profiler);
            }

            return $profiler;
        };

        $app['profiler.storage'] = function() {
            return new SqliteProfilerStorage('sqlite:'.$this->config['file'], '', '', 86400);
        };

        $app['profiler.stopwatch'] = function() {
            return new Stopwatch;
        };

        $app->extend('events', function($dispatcher, $app) {
            return new TraceableEventDispatcher($dispatcher, $app['profiler.stopwatch']);
        });

        $app->on('kernel.boot', function() use ($app) {

            if (isset($app['view'])) {
                $app->extend('view', function($view, $app) {
                    return new TraceableView($view, $app['profiler.stopwatch']);
                });
            }

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
                $app['profiler']->add(new SystemDataCollector($app['info']), 'app/modules/profiler/views/toolbar/system.php', 'app/modules/profiler/views/panel/system.php', 50);
                $app['profiler']->add(new UserDataCollector($app['auth']), 'app/modules/profiler/views/toolbar/user.php', null, -20);
            });

            $app->subscribe(new ProfilerListener($app['profiler']));
            $app->subscribe($request);
            $app->subscribe(new ToolbarListener);

        });
    },

    'autoload' => [

        'Pagekit\\Profiler\\' => 'src'

    ],

    'config' => [

        'file'    => null,
        'enabled' => false

    ]

];
