<?php

use Pagekit\Option\Loader\OptionLoader;
use Pagekit\Option\Option;

return [

    'name' => 'system/option',

    'main' => function ($app, $config) {

        $app['option'] = function ($app) use ($config) {
            return new Option($app['db'], $app['cache'], $config['table']);
        };

        $app['module']->addLoader(new OptionLoader);

    },

    'priority' => 8,

    'autoload' => [

        'Pagekit\\Option\\' => 'src'

    ],

    'table' => '@system_option'

];
