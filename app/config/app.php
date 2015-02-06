<?php

return [

    'system/cache' => [

        'caches' => [

            'cache' => [

                'storage' => 'auto',
                'path'    => '%path%/app/cache',
                'prefix'  => sha1(__DIR__)

            ],

            'cache.phpfile' => [

                'storage' => 'phpfile',
                'path'    => '%path%/app/cache'

            ]

        ]

    ],

    'system/profiler' => [

        'file' => '%path%/app/temp/profiler.db'

    ],

    'framework/session' => [

        'storage'  => 'database',
        'lifetime' => 900,
        'files'    => '%path%/app/sessions',
        'table'    => '@system_session',
        'cookie'   => [
            'name' => 'pagekit_session',
        ]

    ],

    'framework/database' => [

        'connections' => [

            'sqlite' => [

                'driver'  => 'pdo_sqlite',
                'path'    => '%path%/app/database/pagekit.db',
                'charset' => 'utf8',
                'prefix'  => ''

            ],

        ]

    ],

    'framework/filesystem' => [

        'path' => '%path%'

    ]

];
