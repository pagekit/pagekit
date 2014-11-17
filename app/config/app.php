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

            'Pagekit\Framework\Provider\ExceptionServiceProvider',
            'Pagekit\Framework\Provider\RazrServiceProvider',
            'Pagekit\Framework\Provider\TemplatingServiceProvider',
            'Pagekit\Component\Profiler\ProfilerServiceProvider',
            'Pagekit\Component\Cache\CacheServiceProvider',
            'Pagekit\Component\Cookie\CookieServiceProvider',
            'Pagekit\Component\Database\DatabaseServiceProvider',
            'Pagekit\Component\Auth\AuthServiceProvider',
            'Pagekit\Component\Auth\RememberMeServiceProvider',
            'Pagekit\Component\File\FilesystemServiceProvider',
            'Pagekit\Component\File\ResourceLocatorServiceProvider',
            'Pagekit\Component\Mail\MailServiceProvider',
            'Pagekit\Component\Markdown\MarkdownServiceProvider',
            'Pagekit\Component\Migration\MigrationServiceProvider',
            'Pagekit\Component\Option\OptionServiceProvider',
            'Pagekit\Component\Session\CsrfServiceProvider',
            'Pagekit\Component\Session\SessionServiceProvider',
            'Pagekit\Component\Translation\TranslationServiceProvider',
            'Pagekit\Component\View\AssetServiceProvider',
            'Pagekit\Component\View\ViewServiceProvider',
            'Pagekit\SystemServiceProvider'

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

    'locator' => [

        'wrappers' => [

            'app'       => false,
            'storage'   => false,
            'extension' => true,
            'theme'     => true,
            'vendor'    => true

        ],

        'paths' => [

            'app'       => '%path%',
            'storage'   => '%path.storage%',
            'extension' => '%path.extensions%',
            'theme'     => '%path.themes%',
            'vendor'    => '%path.vendor%'

        ]

    ],

    'option' => [

        'table' => '@system_option'

    ],

    'extension' => [

        'path' => '%path.extensions%',
        'core' => ['installer', 'system']

    ],

    'theme' => [

        'path' => '%path.themes%',
        'site' => 'alpha'

    ]

];
