<?php

/**
 * Theme configuration file.
 *
 * @link http://pagekit.com/docs/themes - basic explanation of theme development
 * @link http://pagekit.com/docs/configuration - full documentation of config options
 */
return [

    'name' => 'alpha',

    'type' => 'theme',

    /**
     * Namespace to autoload theme classes.
     */
    'autoload' => [

        'Pagekit\\Alpha\\' => 'src'

    ],

    /**
     * The main function.
     */
    'main' => 'Pagekit\\Alpha\\AlphaTheme',

    /**
     * Overwrite default template files with templates provided by the theme and define stream wrappers for shorter path access.
     */
    'resources' => [

        'alpha:' => ''

    ],

    /**
     * List of renderer views provided by this theme. A renderer view determines the markup to be used in a widget position.
     */
    'views' => [

        'grid' => 'alpha:views/position-grid.php',
        'navbar' => 'alpha:views/position-navbar.php',
        'offcanvas' => 'alpha:views/position-offcanvas.php',
        'panel' => 'alpha:views/position-panel.php',
        'menu' => 'alpha:views/menu.php',
        'menu-navbar' => 'alpha:views/menu-navbar.php'

    ],

    /**
     * Menu postions provided by this theme.
     */
    'menus' => [

        'main' => 'Main',
        'offcanvas' => 'Offcanvas'

    ],

    /**
     * Widget positions provided by this theme. These positions will be rendered in different locations of the theme's template.
     */
    'positions' => [

        'logo' => 'Logo',
        'logo-small' => 'Logo Small',
        'navbar' => 'Navbar',
        'top' => 'Top',
        'sidebar-a' => 'Sidebar A',
        'sidebar-b' => 'Sidebar B',
        'footer' => 'Footer',
        'offcanvas' => 'Offcanvas'

    ],

    'settings' => 'settings-alpha',

    /**
     * Define default settings values and views where end users can change these values.
     */
    'config' => [

        'widget' => [

            'panel' => '',
            'badge' => [
                'text' => '',
                'type' => 'uk-panel-badge uk-badge'
            ],
            'alignment' => ''

        ]

    ],

    'events' => [

        'view.scripts' => function ($event, $scripts) {
            $scripts->register('theme-widget', 'alpha:app/bundle/widget-theme.js', '~widgets');
            $scripts->register('theme-settings', 'alpha:app/bundle/settings.js', '~themes');
        }

    ]

];
