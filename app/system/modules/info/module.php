<?php

use Pagekit\Info\InfoHelper;

return [

    'name' => 'system/info',

    'main' => function ($app) {

        $app['info'] = function() {
            return new InfoHelper();
        };

    },

    'autoload' => [

        'Pagekit\\Info\\' => 'src'

    ],

    'controllers' => [

        '@system/info: /system/info' => [
            'Pagekit\\Info\\Controller\\InfoController'
        ]

    ],

    'menu' => [

        'system: info' => [
            'label'    => 'Info',
            'parent'   => 'system: system',
            'url'      => '@system/info',
            'priority' => 150
        ]

    ]

];
