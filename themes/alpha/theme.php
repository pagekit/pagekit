<?php

return array(

    'positions' => array(

        'logo'          => 'Logo',
        'logo-small'    => 'Logo Small',
        'navbar'        => 'Navbar',
        'top'           => 'Top',
        'sidebar-a'     => 'Sidebar A',
        'sidebar-b'     => 'Sidebar B',
        'footer'        => 'Footer',
        'offcanvas'     => 'Offcanvas'

    ),

    'autoload' => array(

        'Pagekit\\Alpha\\' => 'src'

    ),

    'main' => 'Pagekit\\Alpha\\AlphaTheme',

    'resources' => array(

        'override' => array(
            'extension://system/theme/templates' => 'templates/system'
        )

    ),

    'settings' => array(

        'system'  => 'theme://alpha/views/admin/settings.razr.php',
        'widgets' => 'theme://alpha/views/admin/widgets/edit.razr.php'

    )

);
