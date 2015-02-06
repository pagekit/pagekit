<?php

return [

    'name' => 'system/cache',

    'main' => 'Pagekit\\Cache\\CacheModule',

    'autoload' => [

        'Pagekit\\Cache\\' => 'src'

    ],

    'priority' => 12,

    'config' => [

        'caches' => [],
        'nocache' => false

    ]

];
