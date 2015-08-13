<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\DBAL\Types\Type;
use Pagekit\Database\ORM\EntityManager;
use Pagekit\Database\ORM\Loader\AnnotationLoader;
use Pagekit\Database\ORM\MetadataManager;
use Pagekit\Event\PrefixEventDispatcher;

return [

    'name' => 'database',

    'main' => function ($app) {

        $default = [
            'wrapperClass' => 'Pagekit\Database\Connection'
        ];

        $app['dbs'] = function ($app) use ($default) {

            $dbs = [];

            foreach ($this->config['connections'] as $name => $params) {
                 $dbs[$name] = DriverManager::getConnection(array_replace($default, $params));
            }

            return $dbs;
        };

        $app['db'] = function ($app) {
            return $app['dbs'][$this->config['default']];
        };

        $app['db.em'] = function ($app) {
            return new EntityManager($app['db'], $app['db.metas'], $app['db.events']);
        };

        $app['db.metas'] = function ($app) {

            $manager = new MetadataManager($app['db'], $app['db.events']);
            $manager->setLoader(new AnnotationLoader);
            $manager->setCache($app['cache.phpfile']);

            return $manager;
        };

        $app['db.events'] = function ($app) {
            return new PrefixEventDispatcher('model.', $app['events']);
        };

        $app['db.debug_stack'] = function ($app) {
            return new DebugStack();
        };

        Type::overrideType(Type::SIMPLE_ARRAY, '\Pagekit\Database\Types\SimpleArrayType');
        Type::overrideType(Type::JSON_ARRAY, '\Pagekit\Database\Types\JsonArrayType');
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
