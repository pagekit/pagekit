<?php

use Pagekit\Profiler\ProfilerModule;

return [

    'name' => 'system/profiler',

    'main' => function ($app, $config) {

        $module = new ProfilerModule();
        $module->load($app, $config);

    },

    'autoload' => [

        'Pagekit\\Profiler\\' => 'src'

    ],

    'file'    => null,
    'enabled' => false

];
