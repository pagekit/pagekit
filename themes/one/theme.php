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

        'theme:' => '',
        'views:' => 'views'

    ],

    /**
     * Menu positions provided by this theme.
     */
    'menus' => [

        'primary' => 'Primary',
        'offcanvas' => 'Offcanvas'

    ],

    /**
     * Widget positions provided by this theme. These positions will be rendered in different locations of the theme's template.
     */
    'positions' => [

        'logo' => 'Logo',
        'hero' => 'Hero',
        'top' => 'Top',
        'sidebar' => 'Sidebar',
        'bottom' => 'Bottom',
        'footer' => 'Footer',
        'offcanvas' => 'Offcanvas'

    ],

    'settings' => 'settings-theme',

    /**
     * Define default settings values and views where end users can change these values.
     */
    'config' => [

        'sidebar-first' => false,
        'hero-image' => '',

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
