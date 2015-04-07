<?php

use Pagekit\System\Info\InfoHelper;

return [

    'name' => 'system/info',

    'main' => function ($app) {

        $app['systemInfo'] = function() {
            return new InfoHelper();
        };

    },

    'autoload' => [

        'Pagekit\\System\\Info\\' => 'src'

    ],

    'controllers' => [

        '@system: /system' => [
            'Pagekit\\System\\Info\\InfoController'
        ]

    ],

    'menu' => [

        'system: info' => [
            'label'    => 'Info',
            'parent'   => 'system: settings',
            'url'      => '@system/info'
        ]

    ]

];
