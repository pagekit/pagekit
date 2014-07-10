<?php

return [

    'main' => 'Pagekit\\Installer\\InstallerExtension',

    'autoload' => [

        'Pagekit\\' => '../system/src',
        'Pagekit\\Installer\\' => 'src'

    ],

    'resources' => [

        'export' => [
            'view'  => 'views',
            'asset' => 'assets'
        ]

    ],

    'controllers' => 'src/Controller/*Controller.php'

];
