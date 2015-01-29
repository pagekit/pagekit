<?php

use Pagekit\Profiler\DataCollector\SystemDataCollector;
use Pagekit\Profiler\DataCollector\UserDataCollector;

return [

    'name' => 'system/profiler',

    'main' => function ($app, $config) {

        if (isset($app['profiler'])) {
            $app->on('system.init', function() use ($app) {
                $app['profiler']->add(new SystemDataCollector($app['systemInfo']), 'extensions/system/modules/profiler/views/toolbar/system.php', 'extensions/system/modules/profiler/views/panel/system.php', 50);
                $app['profiler']->add(new UserDataCollector($app['auth']), 'extensions/system/modules/profiler/views/toolbar/user.php', null, -20);
            });
        }

    },

    'autoload' => [

        'Pagekit\\Profiler\\' => 'src'

    ]

];
