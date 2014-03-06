<?php

return array(

    'main' => 'Pagekit\\Installer\\InstallerExtension',

    'autoload' => array(

        'Pagekit\\' => '../system/src',
        'Pagekit\\Installer\\' => 'src'

    ),

    'resources' => array(

        'export' => array(
            'view'  => 'views',
            'asset' => 'assets'
        )

    ),

    'controllers' => 'src/Controller/*Controller.php'

);
