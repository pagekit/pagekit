<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return [

    'name' => 'installer',

    'main' => function ($app) {

        if (!$this->config['enabled']) {
            return false;
        }

        $app->on('kernel.request', function ($event) use ($app) {
            if ($locale = $app['request']->getPreferredLanguage()) {
                $app['translator']->setLocale($locale);
            }
        });

        $app->error(function (NotFoundHttpException $e) use ($app) {
            return $app['response']->redirect('@installer/installer');
        });

    },

    'require' => [

        'application',
        'migration',
        'system/cache',
        'system/locale',
        'system/option',
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
        'sampleData' => __DIR__.'/sample_data.sql'

    ]

];
