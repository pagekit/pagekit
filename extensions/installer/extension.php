<?php

return [

    'name' => 'installer',

    'main' => 'Pagekit\\Installer\\InstallerExtension',

    'autoload' => [

        'Pagekit\\System\\' => '../system/src',
        'Pagekit\\Installer\\' => 'src'

    ],

    'controllers' => [

        '@installer: /installer' => 'Pagekit\\Installer\\Controller\\InstallerController'

    ],

    'config' => [

        'enabled' => false

    ]

];
