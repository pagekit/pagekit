<?php

return [

    'application' => [

        'version' => '1.0.13'

    ],

    'auth' => [

        'table' => '@system_auth',
        'cookie' => [
            'name' => 'pagekit_auth',
            'lifetime' => 315360000
        ]

    ],

    'debug' => [

        'file' => "sqlite:$path/tmp/temp/debug.db"

    ],

    'session' => [

        'storage' => 'database',
        'lifetime' => 900,
        'files' => "$path/tmp/sessions",
        'table' => '@system_session',
        'cookie' => [
            'name' => 'pagekit_session',
            'httponly' => true
        ]

    ],

    'filesystem' => [

        'path' => $path

    ],

    'system/cache' => [

        'caches' => [

            'cache' => [

                'storage' => 'auto',
                'path' => "$path/tmp/cache",
                'prefix' => sha1($path)

            ],

            'cache.phpfile' => [

                'storage' => 'phpfile',
                'path' => "$path/tmp/cache"

            ]

        ]

    ],

    'system/dashboard' => [

        'defaults' => [

            'userdefault' => [
                'id' => 'userdefault',
                'type' => 'user',
                'show' => 'login',
                'display' => 'thumbnail'
            ]
        ]

    ]

];
