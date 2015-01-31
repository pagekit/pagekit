<?php

use Pagekit\Option\Option;

return [

    'name' => 'system/option',

    'main' => function ($app, $config) {

        $app['option'] = function ($app) use ($config) {
            return new Option($app['db'], $app['cache'], $config['table']);
        };

    },

    'autoload' => [

        'Pagekit\\Option\\' => 'src'

    ],

    'table' => '@system_option'

];
