<?php

return [

    'autoload' => [

        'Pagekit\\Alpha\\' => 'src'

    ],

    'main' => 'Pagekit\\Alpha\\AlphaTheme',

    'resources' => [

        'override' => [
            'extension://system/theme/templates' => 'templates/system'
        ]

    ],

    'settings' => [

        'system'  => 'theme://alpha/views/admin/settings.razr',
        'widgets' => 'theme://alpha/views/admin/widgets/edit.razr'

    ],

    'positions' => [

        'logo'       => 'Logo',
        'logo-small' => 'Logo Small',
        'navbar'     => 'Navbar',
        'top'        => 'Top',
        'sidebar-a'  => 'Sidebar A',
        'sidebar-b'  => 'Sidebar B',
        'footer'     => 'Footer',
        'offcanvas'  => 'Offcanvas'

    ],

    'renderer' => [

        'blank'     => 'theme://alpha/views/renderer/position.blank.razr',
        'grid'      => 'theme://alpha/views/renderer/position.grid.php',
        'navbar'    => 'theme://alpha/views/renderer/position.navbar.razr',
        'offcanvas' => 'theme://alpha/views/renderer/position.offcanvas.razr',
        'panel'     => 'theme://alpha/views/renderer/position.panel.razr'

    ]

];
