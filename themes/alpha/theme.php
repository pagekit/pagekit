<?php

/**
 * Theme configuration.
 *
 * @link http://pagekit.com/docs/configuration Full documentation on all usable parameters
 */
return [

    /**
     * Namespace to autoload theme classes.
     *
     * @link http://pagekit.com/docs/configuration#autoload
     */
    'autoload' => [

        'Pagekit\\Alpha\\' => 'src'

    ],

    /**
     * The main theme class to be loaded when the theme is booted.
     *
     * @link http://pagekit.com/docs/configuration#main
     */
    'main' => 'Pagekit\\Alpha\\AlphaTheme',

    'resources' => [

        /**
         * Overwrite default template files with templates provided by the theme.
         *
         * @link http://pagekit.com/docs/configuration#resources
         */
        'override' => [
            'extension://system/theme/templates' => 'templates/system'
        ]

    ],

    'settings' => [

        'system'  => 'theme://alpha/views/admin/settings.razr',
        'widgets' => 'theme://alpha/views/admin/widgets/edit.razr'

    ],

    /**
     * Widget positions offered by this theme. These positions will be rendered in
     * different locations of the theme's template.
     *
     * @link http://pagekit.com/docs/configuration#positions
     */
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

    /**
     * List of renderers provided by this theme. A renderer determines the markup to
     * be used in a widget position.
     *
     * @link http://pagekit.com/docs/themes#renderer
     */
    'renderer' => [

        'blank'     => 'theme://alpha/views/renderer/position.blank.razr',
        'grid'      => 'theme://alpha/views/renderer/position.grid.php',
        'navbar'    => 'theme://alpha/views/renderer/position.navbar.razr',
        'offcanvas' => 'theme://alpha/views/renderer/position.offcanvas.razr',
        'panel'     => 'theme://alpha/views/renderer/position.panel.razr'

    ]

];
