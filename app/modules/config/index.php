<?php

use Pagekit\Config\ConfigManager;

return [

    'name' => 'config',

    'main' => function ($app) {

        $app['config'] = function ($app) {
            return new ConfigManager($app['db'], $this->config);
        };

        if ($app['config.file']) {
            $app['module']->addLoader(function ($module) use ($app) {

                if ($app['config']->has($module['name'])) {
                    $module = array_replace_recursive($module, [
                        'config' => $app['config']->get($module['name'])->toArray()
                    ]);
                }

                return $module;
            });
        }

    },

    'require' => [

        'database'

    ],

    'autoload' => [

        'Pagekit\\Config\\' => 'src'

    ],

    'config' => [

        'table'  => '@system_config'

    ],

    'events' => [

        'terminate' => [function () use ($app) {
            foreach ($app['config'] as $name => $config) {
                $app['config']->set($name, $config);
            }
        }, 100]

    ]

];
