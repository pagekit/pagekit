<?php

/**
 * Configuration
 */
return [

    'name' => 'one',

    'type' => 'theme',

    /**
     * Define resources
     */
    'resources' => [

        'theme:' => '',
        'views:' => 'views'

    ],

    /**
     * Define menu positions
     */
    'menus' => [

        'primary' => 'Primary',
        'offcanvas' => 'Offcanvas'

    ],

    /**
     * Define widget positions
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
     * Define settings
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
