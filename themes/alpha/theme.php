<?php

use Pagekit\Alpha\AlphaTheme;

/**
 * Theme configuration file.
 *
 * @link http://pagekit.com/docs/themes - basic explanation of theme development
 * @link http://pagekit.com/docs/configuration - full documentation of config options
 */
return [

    'name' => 'alpha',

    /**
     * The main function.
     */
    'main' => function ($app, $config) {

        return new AlphaTheme($app, $config);

    },

    /**
     * Namespace to autoload theme classes.
     */
    'autoload' => [

        'Pagekit\\Alpha\\' => 'src'

    ],

    /**
     * Overwrite default template files with templates provided by the theme and define stream wrappers for shorter path access.
     */
    'resources' => [

        'override' => [
            'extensions/system/theme/templates' => 'templates/system'
        ]

    ],

    /**
     * Define default settings values and views where end users can change these values.
     */
    'parameters' => [

        'settings' => [
            'view' => 'themes/alpha/views/admin/settings.razr'
        ],

        'widgets'  => [
            'view' => 'themes/alpha/views/admin/widgets/edit.razr'
        ]

    ],

    /**
     * Widget positions offered by this theme. These positions will be rendered in different locations of the theme's template.
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
     * List of renderers provided by this theme. A renderer determines the markup to be used in a widget position.
     */
    'renderer' => [

        'blank'     => 'themes/alpha/views/renderer/position.blank.razr',
        'grid'      => 'themes/alpha/views/renderer/position.grid.php',
        'navbar'    => 'themes/alpha/views/renderer/position.navbar.razr',
        'offcanvas' => 'themes/alpha/views/renderer/position.offcanvas.razr',
        'panel'     => 'themes/alpha/views/renderer/position.panel.razr'

    ],

    /**
     * List of fixed menus provided by this theme.
     */
    'menus' => [

        'main'    => 'Main',
        'sidebar' => 'Sidebar'

    ]

];
