<?php

use Pagekit\Option\Loader\OptionLoader;
use Pagekit\Option\Option;

return [

    'name' => 'system/option',

    'main' => function ($app) {

        $app['option'] = function ($app) {
            return new Option($app['db'], $app['cache'], $this->config['table']);
        };

        $app['module']->addLoader(new OptionLoader);

    },

    'require' => [

        'framework/database',
        'system/cache'

    ],

    'autoload' => [

        'Pagekit\\Option\\' => 'src'

    ],

    'config' => [

        'table' => '@system_option'

    ]

];
