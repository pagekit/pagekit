<?php

return [

    'app' => [

        'version' => '0.8.8',

        'debug' => true,

        'storage' => '/storage',

        'providers' => [

            'Pagekit\Application\Provider\ExceptionServiceProvider',
            'Pagekit\Profiler\ProfilerServiceProvider',
            'Pagekit\Cookie\CookieServiceProvider',
            'Pagekit\Database\DatabaseServiceProvider',
            'Pagekit\Auth\AuthServiceProvider',
            'Pagekit\Auth\RememberMeServiceProvider',
            'Pagekit\Feed\FeedServiceProvider',
            'Pagekit\Filesystem\FilesystemServiceProvider',
            'Pagekit\Markdown\MarkdownServiceProvider',
            'Pagekit\Migration\MigrationServiceProvider',
            'Pagekit\Session\CsrfServiceProvider',
            'Pagekit\Session\SessionServiceProvider',
            'Pagekit\View\AssetServiceProvider',
            'Pagekit\View\ViewServiceProvider',
            'Pagekit\System\SystemServiceProvider',
            'Pagekit\Application\Provider\RazrServiceProvider',
            'Pagekit\Application\Provider\TemplatingServiceProvider',

        ]

    ],

    'api' => [

        'url' => 'http://pagekit.com/api'

    ],

    'profiler' => [

        'file' => '%path%/app/temp/profiler.db'

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

    'session' => [

        'storage'  => 'database',
        'lifetime' => 900,
        'files'    => '%path%/app/sessions',
        'table'    => '@system_session',
        'cookie'   => [
            'name' => 'pagekit_session',
        ]

    ],

    'database' => [

        'default' => 'mysql',

        'connections' => [

            'mysql' => [

                'driver'   => 'pdo_mysql',
                'dbname'   => '',
                'host'     => 'localhost',
                'user'     => 'root',
                'password' => '',
                'engine'   => 'InnoDB',
                'charset'  => 'utf8',
                'collate'  => 'utf8_unicode_ci',
                'prefix'   => ''

            ],

            'sqlite' => [

                'driver'  => 'pdo_sqlite',
                'path'    => '%path%/app/database/pagekit.db',
                'charset' => 'utf8',
                'prefix'  => ''

            ],

        ]

    ],

    'extension' => [

        'core' => ['installer', 'system']

    ],

    'theme' => [

        'site' => 'alpha'

    ]

];
