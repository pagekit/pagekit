<?php

use Pagekit\Installer\InstallerExtension;

return [

    'name' => 'installer',

    'main' => function ($app, $config) {

        return new InstallerExtension($app, $config);

    },

    'autoload' => [

        'Pagekit\\' => '../system/src',
        'Pagekit\\Installer\\' => 'src'

    ],

    'controllers' => [

        '@installer: /installer' => 'Pagekit\\Installer\\Controller\\InstallerController'

    ]

];
