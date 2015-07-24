<?php

use Pagekit\Filter\FilterManager;

return [

    'name' => 'filter',

    'main' => function ($app) {

        $app['filter'] = function() {
            return new FilterManager($this->config['defaults']);
        };

    },

    'autoload' => [

        'Pagekit\\Filter\\' => 'src'

    ],

    'config' => [

        'defaults' => null

    ]
];
