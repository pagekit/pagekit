<?php

return [

    'app' => [

        'version' => '0.8.8',

        'debug' => true,

        'storage' => '/storage'

    ],

    'api' => [

        'url' => 'http://pagekit.com/api'

    ],

    'system/cache' => [

        'config' => [

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

        'config.path' => '%path%'

    ],

    'extension' => [

        'core' => ['installer', 'system']

    ],

    'theme' => [

        'site' => 'alpha'

    ]

];
