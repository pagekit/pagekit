<?php

use Pagekit\Kernel\Exception\NotFoundException;

return [

    'name' => 'installer',

    'main' => function ($app) {

        if (!$this->config['enabled']) {
            return false;
        }

        $app->on('request', function ($event) use ($app) {
            if ($locale = $app['request']->getPreferredLanguage()) {
                $app['translator']->setLocale($locale);
            }
        });

        $app->error(function (NotFoundException $e) use ($app) {
            return $app['response']->redirect('@installer');
        });

    },

    'require' => [

        'application',
        'migration',
        'system/cache',
        'system/intl',
        'system/view'

    ],

    'autoload' => [

        'Pagekit\\System\\' => '../system/src',
        'Pagekit\\Installer\\' => 'src'

    ],

    'routes' => [

        '/installer' => [
            'name' => '@installer',
            'controller' => 'Pagekit\\Installer\\InstallerController'
        ]

    ],

    'languages' => '/../system/languages',

    'config' => [

        'enabled' => false,
        'sampleData' => ''

    ]

];
