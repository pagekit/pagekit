<?php

return [

    'app' => [

        'version' => '0.8.8',

        'debug' => true,

        'timezone' => 'UTC',

        'locale' => 'en_US',

        'locale_admin' => 'en_US',

        'storage' => '/storage',

        'providers' => [

            'Pagekit\Application\Provider\ExceptionServiceProvider',
            'Pagekit\Application\Provider\RazrServiceProvider',
            'Pagekit\Application\Provider\TemplatingServiceProvider',
            'Pagekit\Profiler\ProfilerServiceProvider',
            'Pagekit\Cache\CacheServiceProvider',
            'Pagekit\Cookie\CookieServiceProvider',
            'Pagekit\Database\DatabaseServiceProvider',
            'Pagekit\Auth\AuthServiceProvider',
            'Pagekit\Auth\RememberMeServiceProvider',
            'Pagekit\Feed\FeedServiceProvider',
            'Pagekit\Filesystem\FilesystemServiceProvider',
            'Pagekit\Mail\MailServiceProvider',
            'Pagekit\Markdown\MarkdownServiceProvider',
            'Pagekit\Migration\MigrationServiceProvider',
            'Pagekit\Option\OptionServiceProvider',
            'Pagekit\Session\CsrfServiceProvider',
            'Pagekit\Session\SessionServiceProvider',
            'Pagekit\View\AssetServiceProvider',
            'Pagekit\View\ViewServiceProvider',
            'Pagekit\System\SystemServiceProvider'

        ]

    ],

    'api' => [

        'url' => 'http://pagekit.com/api'

    ],

    'profiler' => [

        'file' => '%path%/app/temp/profiler.db'

    ],

    'cache' => [

        'cache' => [

            'storage' => 'auto',
            'path'    => '%path%/app/cache',
            'prefix'  => sha1(__DIR__)

        ],

        'cache.phpfile' => [

            'storage' => 'phpfile',
            'path'    => '%path%/app/cache'

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

    'mail' => [

        'driver'     => 'mail',
        'host'       => 'localhost',
        'port'       => 25,
        'encryption' => null,
        'username'   => null,
        'password'   => null,
        'from'       => ['address' => null, 'name' => null]

    ],

    'option' => [

        'table' => '@system_option'

    ],

    'extension' => [

        'core' => ['installer', 'system']

    ],

    'theme' => [

        'site' => 'alpha'

    ]

];
