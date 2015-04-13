<?php

use Pagekit\Config\ConfigManager;

return [

    'name' => 'config',

    'main' => function ($app) {

        $app['config'] = function ($app) {
            return new ConfigManager($app['db'], $app['cache'], $this->config['table']);
        };

        if ($app['config.file']) {
            $app['module']->addLoader(function($name, array $config) use ($app) {

                if (is_array($values = $app['config']->get($name, []))) {
                    $config = array_replace_recursive($config, ['config' => $values]);
                }

                return $config;
            });
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
