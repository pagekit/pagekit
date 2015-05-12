<?php

use Pagekit\Kernel\Exception\NotFoundException;

return [

    'name' => 'installer',

    'main' => function ($app) {

        if (!$this->config['enabled']) {
            return false;
        }

        $app->on('app.request', function ($event) use ($app) {
            if ($locale = $app['request']->getPreferredLanguage()) {
                $app['translator']->setLocale($locale);
            }
        });

        $app->error(function (NotFoundException $e) use ($app) {
            return $app['response']->redirect('@installer/installer');
        });

    },

    'require' => [

        'application',
        'migration',
        'system/cache',
        'system/view'

    ],

    'autoload' => [

        'Pagekit\\System\\' => '../system/src',
        'Pagekit\\Installer\\' => 'src'

    ],

    'controllers' => [

        '@installer: /' => 'Pagekit\\Installer\\InstallerController'

    ],

    'config' => [

        'enabled'    => false,
        'sampleData' => ''

    ]

];
