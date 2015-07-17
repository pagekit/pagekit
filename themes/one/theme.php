<?php

/**
 * Theme configuration file.
 *
 * @link http://pagekit.com/docs/themes - basic explanation of theme development
 * @link http://pagekit.com/docs/configuration - full documentation of config options
 */
return [

    'name' => 'one',

    'type' => 'theme',

    /**
     * Overwrite default template files with templates provided by the theme and define stream wrappers for shorter path access.
     */
    'resources' => [

        'theme:' => ''

    ],

    /**
     * List of named views provided by this theme.
     */
    'views' => [

        'grid' => 'theme:views/position-grid.php',
        'navbar' => 'theme:views/position-navbar.php',
        'offcanvas' => 'theme:views/position-offcanvas.php',
        'panel' => 'theme:views/position-panel.php',
        'menu' => 'theme:views/menu.php',
        'menu-navbar' => 'theme:views/menu-navbar.php'

    ],

    /**
     * Menu positions provided by this theme.
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

    'settings' => 'settings-theme',

    /**
     * Define default settings values and views where end users can change these values.
     */
    'config' => [

        'sidebars' => [
            'sidebar-a' => [
                'width' => 12,
                'first' => false
            ],
            'sidebar-b' => [
                'width' => 12,
                'first' => false
            ]
        ],

        'blocks' => [
            'top-a' => [
                'background' => 'default',
                'image' => '',
                'contrast' => '',
                'padding' => '',
                'width' => false,
                'height' => false
            ],
            'top-b' => [
                'background' => 'default',
                'image' => '',
                'contrast' => '',
                'padding' => '',
                'width' => false,
                'height' => false
            ],
            'top-c' => [
                'background' => 'default',
                'image' => '',
                'contrast' => '',
                'padding' => '',
                'width' => false,
                'height' => false
            ],
            'top-d' => [
                'background' => 'default',
                'image' => '',
                'contrast' => '',
                'padding' => '',
                'width' => false,
                'height' => false
            ],
            'main' => [
                'background' => 'default',
                'image' => '',
                'contrast' => '',
                'padding' => '',
                'width' => false,
                'height' => false
            ],
            'bottom-a' => [
                'background' => 'default',
                'image' => '',
                'contrast' => '',
                'padding' => '',
                'width' => false,
                'height' => false
            ],
            'bottom-b' => [
                'background' => 'default',
                'image' => '',
                'contrast' => '',
                'padding' => '',
                'width' => false,
                'height' => false
            ],
            'bottom-c' => [
                'background' => 'default',
                'image' => '',
                'contrast' => '',
                'padding' => '',
                'width' => false,
                'height' => false
            ],
            'bottom-d' => [
                'background' => 'default',
                'image' => '',
                'contrast' => '',
                'padding' => '',
                'width' => false,
                'height' => false
            ],
            'footer' => [
                'background' => 'default',
                'image' => '',
                'contrast' => '',
                'padding' => '',
                'width' => false,
                'height' => false
            ]
        ],

        'grid' => [
            'top-a' => [
                'layout' => 'parallel',
                'responsive' => '',
                'divider' => false
            ],
            'top-b' => [
                'layout' => 'parallel',
                'responsive' => '',
                'divider' => false
            ],
            'top-c' => [
                'layout' => 'parallel',
                'responsive' => '',
                'divider' => false
            ],
            'top-d' => [
                'layout' => 'parallel',
                'responsive' => '',
                'divider' => false
            ],
            'bottom-a' => [
                'layout' => 'parallel',
                'responsive' => '',
                'divider' => false
            ],
            'bottom-b' => [
                'layout' => 'parallel',
                'responsive' => '',
                'divider' => false
            ],
            'bottom-c' => [
                'layout' => 'parallel',
                'responsive' => '',
                'divider' => false
            ],
            'bottom-d' => [
                'layout' => 'parallel',
                'responsive' => '',
                'divider' => false
            ],
            'main-top' => [
                'layout' => 'parallel',
                'responsive' => '',
                'divider' => false
            ],
            'main-bottom' => [
                'layout' => 'parallel',
                'responsive' => '',
                'divider' => false
            ]
        ],

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
            $scripts->register('theme-widget', 'theme:app/bundle/widget-theme.js', '~widgets');
            $scripts->register('theme-settings', 'theme:app/bundle/settings.js', '~themes');
        }

    ]

];
