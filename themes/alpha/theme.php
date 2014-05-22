<?php

return array(

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

    ),

    'positions' => array(

        'logo'       => 'Logo',
        'logo-small' => 'Logo Small',
        'navbar'     => 'Navbar',
        'top'        => 'Top',
        'sidebar-a'  => 'Sidebar A',
        'sidebar-b'  => 'Sidebar B',
        'footer'     => 'Footer',
        'offcanvas'  => 'Offcanvas'

    ),

    'renderer' => array(

        'blank'     => 'theme://alpha/views/renderer/position.blank.razr.php',
        'grid'      => 'theme://alpha/views/renderer/position.grid.php',
        'navbar'    => 'theme://alpha/views/renderer/position.navbar.razr.php',
        'offcanvas' => 'theme://alpha/views/renderer/position.offcanvas.razr.php',
        'panel'     => 'theme://alpha/views/renderer/position.panel.razr.php'

    )

);
