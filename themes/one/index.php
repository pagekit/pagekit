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

    'settings' => '@site/settings#site-theme',

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
            $scripts->register('widget-theme', 'theme:app/bundle/widget-theme.js', '~widgets');
        },

        'view.system/site/admin/settings' => function ($event, $view) {
            $view->script('site-theme', 'theme:app/bundle/site-theme.js', 'site-settings');
            $view->data('$theme', $this);
        }

    ]

];
