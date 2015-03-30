<?php

use Pagekit\Option\Loader\OptionLoader;
use Pagekit\Option\Option;

return [

    'name' => 'option',

    'main' => function ($app) {

        $app['option'] = function ($app) {
            return new Option($app['db'], $app['cache'], $this->config['table']);
        };

        if ($app['config.file']) {
            $app['module']->addLoader(new OptionLoader);
        }

    },

    'require' => [

        'database',
        'cache'

    ],

    'autoload' => [

        'Pagekit\\Option\\' => 'src'

    ],

    'config' => [

        'table' => '@system_option'

    ]

];
