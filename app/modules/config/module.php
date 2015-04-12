<?php

use Pagekit\Config\ConfigManager;
use Pagekit\Config\Loader\ConfigLoader;

return [

    'name' => 'config',

    'main' => function ($app) {

        $app['config'] = function ($app) {
            return new ConfigManager($app['db'], $app['cache'], $this->config['table']);
        };

        if ($app['config.file']) {
            $app['module']->addLoader(new ConfigLoader());
        }

    },

    'require' => [

        'database',
        'system/cache'

    ],

    'autoload' => [

        'Pagekit\\Config\\' => 'src'

    ],

    'config' => [

        'table' => '@system_config'

    ]

];
