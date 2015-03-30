<?php return [
    'database' =>
        [
            'connections' =>
                [
                    'mysql' =>
                        [
                            'host' => 'localhost',
                            'user' => 'root',
                            'password' => 'root',
                            'dbname' => 'pagekit_2',
                            'prefix' => 'pk_',
                        ],
                ],
        ],
    'app' =>
        [
            'site_title' => 'Demo',
            'locale' => 'en',
            'key' => '63f20fb21acb3e8f85febfe0d0fa071e942eb7da',
            'site_description' => '',
            'timezone' => 'utc',
            'debug' => '0',
        ],
    'storage' => '',
    'mail' =>
        [
            'from' =>
                [
                    'address' => '',
                    'name' => '',
                ],
            'driver' => 'mail',
            'port' => '',
            'host' => '',
            'username' => 'admin',
            'password' => 'admin',
            'encryption' => '',
        ],
    'local_date_format' => '',
    'local_time_format' => '',
    'local_firstdayofweek' => '0',
    'cache' =>
        [
            'storage' => 'auto',
        ],
    'profiler' =>
        [
            'enabled' => '0',
        ],
    'maintenance' =>
        [
            'enabled' => '1',
            'msg' => '',
        ],
];