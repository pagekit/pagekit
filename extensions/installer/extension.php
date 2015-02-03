<?php

use Pagekit\Installer\InstallerExtension;

return [

    'name' => 'installer',

    'main' => function ($app, $config) {

        return new InstallerExtension($app, $config);

    },

    'autoload' => [

        'Pagekit\\System\\' => '../system/src',
        'Pagekit\\Installer\\' => 'src'

    ],

    'controllers' => [

        '@installer: /installer' => 'Pagekit\\Installer\\Controller\\InstallerController'

    ]

];
