<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\DBAL\Types\Type;
use Pagekit\Database\ORM\EntityManager;
use Pagekit\Database\ORM\Loader\AnnotationLoader;
use Pagekit\Database\ORM\MetadataManager;

return [

    'name' => 'database',

    'main' => function ($app) {

        $default = [
            'wrapperClass' => 'Pagekit\Database\Connection'
        ];

        $app['dbs'] = function ($app) use ($default) {

            $dbs = [];

            foreach ($this->config['connections'] as $name => $params) {

                $params = array_replace($default, $params);

                if ($this->config['default'] === $name) {
                    $params['events'] = $app['events'];
                }

                $dbs[$name] = DriverManager::getConnection($params);
            }

            return $dbs;
        };

        $app['db'] = function ($app) {
            return $app['dbs'][$this->config['default']];
        };

        $app['db.em'] = function ($app) {
            return new EntityManager($app['db'], $app['db.metas']);
        };

        $app['db.metas'] = function ($app) {

            $manager = new MetadataManager($app['db']);
            $manager->setLoader(new AnnotationLoader);
            $manager->setCache($app['cache.phpfile']);

            return $manager;
        };

        $app['db.debug_stack'] = function ($app) {
            return new DebugStack();
        };

    },

    'autoload' => [

        'Pagekit\\Database\\' => 'src'

    ],

    'config' => [

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

            ]

        ]

    ]

];
