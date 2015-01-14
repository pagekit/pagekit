<?php

return [

    'main' => 'Pagekit\\Installer\\InstallerExtension',

    'autoload' => [

        'Pagekit\\' => '../system/src',
        'Pagekit\\Installer\\' => 'src'

    ],

    'controllers' => [

        '/installer' => 'Pagekit\\Installer\\Controller\\InstallerController'

    ]

];
