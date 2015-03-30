<?php

return [

    'name' => 'cache',

    'main' => 'Pagekit\\Cache\\CacheModule',

    'autoload' => [

        'Pagekit\\Cache\\' => 'src'

    ],

    'controllers' => [

        '@system: /system' => [
            'Pagekit\\Cache\\Controller\\CacheController'
        ],

    ],

    'config' => [

        'caches'  => [],
        'nocache' => false

    ]

];
