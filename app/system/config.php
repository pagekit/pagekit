<?php

return [

    'application' => [

        'version' => '0.8.8'

    ],

    'debug' => [

        'file' => "sqlite:$path/app/database/debug.db"

    ],

    'session' => [

        'storage' => 'database',
        'lifetime' => 900,
        'files' => "$path/tmp/sessions",
        'table' => '@system_session',
        'cookie' => [
            'name' => 'pagekit_session',
        ]

    ],

    'database' => [

        'connections' => [

            'sqlite' => [

                'driver' => 'pdo_sqlite',
                'path' => "$path/app/database/pagekit.db",
                'charset' => 'utf8',
                'prefix' => '',
                'driverOptions' => [
                    'userDefinedFunctions' => [
                        'REGEXP' => [
                            'callback' => function($pattern, $subject) {
                                return preg_match("/$pattern/", $subject);
                            },
                            'numArgs' => 2
                        ]
                    ]
                ]

            ],

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
