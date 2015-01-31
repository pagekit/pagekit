<?php

use Pagekit\Installer\InstallerExtension;

return [

    'name' => 'installer',

    'main' => function ($app, $config) {

        $extension = new InstallerExtension();
        $extension->setConfig($config);
        $extension->load($app, $config);

        return $extension;
    },

    'autoload' => [

        'Pagekit\\' => '../system/src',
        'Pagekit\\Installer\\' => 'src'

    ],

    'controllers' => [

        '@installer: /installer' => 'Pagekit\\Installer\\Controller\\InstallerController'

    ]

];
