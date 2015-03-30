<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return [

    'name' => 'system/installer',

    'main' => function ($app) {

        if (!$this->config['enabled']) {
            return false;
        }

        $app->error(function (NotFoundHttpException $e) use ($app) {
            return $app['response']->redirect('@installer/installer');
        });

        $app->on('system.loaded', function () use ($app) {
            if ($locale = $app['request']->getPreferredLanguage()) {
                $app['translator']->setLocale($locale);
            }
        });

    },

    'require' => [

        'system/core',
        'migration'

    ],

    'autoload' => [

        'Pagekit\\System\\' => '../system/src',
        'Pagekit\\Installer\\' => 'src'

    ],

    'controllers' => [

        '@installer: /installer' => 'Pagekit\\Installer\\Controller\\InstallerController'

    ],

    'config' => [

        'enabled'    => false,
        'sampleData' => __DIR__.'/sample_data.sql'

    ]

];
