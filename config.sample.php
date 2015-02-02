<?php return [
    'database' =>
        [
            'default'     => 'mysql',
            'connections' =>
                [
                    'mysql' =>
                        [
                            'host'     => 'localhost',
                            'user'     => 'DATABASE_USER',
                            'password' => 'DATABASE_PASSWORD',
                            'dbname'   => 'DATABASE_NAME',
                            'prefix'   => 'pk_',
                        ],
                ],
        ],
    'app'      =>
        [
            'key'     => 'UNIQUE_KEY',
            'debug'   => '0',
            'nocache' => '0',
            'storage' => '',
        ],
    'cache'    =>
        [
            'cache' =>
                [
                    'storage' => 'auto',
                ],
        ],
    'profiler' =>
        [
            'enabled' => '0',
        ],
];