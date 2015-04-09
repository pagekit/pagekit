<?php

return [

    'session' => [

        'storage'  => 'database',
        'lifetime' => 900,
        'files'    => '%path%/tmp/sessions',
        'table'    => '@system_session',
        'cookie'   => [
            'name' => 'pagekit_session',
        ]

    ],

    'database' => [

        'connections' => [

            'sqlite' => [

                'driver'  => 'pdo_sqlite',
                'path'    => '%path%/app/database/pagekit.db',
                'charset' => 'utf8',
                'prefix'  => ''

            ],

        ]

    ],

    'filesystem' => [

        'path' => '%path%'

    ],

    'profiler' => [

        'file' => '%path%/app/database/profiler.db'

    ],

    'system/cache' => [

        'caches' => [

            'cache' => [

                'storage' => 'auto',
                'path'    => '%path%/tmp/cache',
                'prefix'  => sha1(__DIR__)

            ],

            'cache.phpfile' => [

                'storage' => 'phpfile',
                'path'    => '%path%/tmp/cache'

            ]

        ]

    ]

];
