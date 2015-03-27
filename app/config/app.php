<?php

return [

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

    ],

    'system/profiler' => [

        'file' => '%path%/app/database/profiler.db'

    ],

    'framework/session' => [

        'storage'  => 'database',
        'lifetime' => 900,
        'files'    => '%path%/tmp/sessions',
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
