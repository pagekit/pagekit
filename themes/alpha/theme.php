<?php

return array(

    'positions' => array(

        'logo'          => 'Logo',
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

    'settings' => '@alpha/settings/index',

);
