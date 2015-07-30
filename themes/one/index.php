<?php

use Pagekit\Site\Model\Node;
use Pagekit\Widget\Model\Widget;

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

        'main' => 'Main',
        'offcanvas' => 'Offcanvas'

    ],

    /**
     * Define widget positions
     */
    'positions' => [

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

        'node' => [

            'alignment' => ''

        ],

        'widget' => [

            'panel' => '',
            'title_size' => 'uk-panel-title',
            'alignment' => ''

        ]

    ],

    'events' => [

        'boot' => function ($event, $app) {

            $self = $this;

            Node::setProperty('theme', function () use ($self) {

                $config  = $self->get("data.nodes.".$this->id, []);
                $default = $self->config("node", []);

                return array_replace($default, $config);

            }, function ($value) use ($self) {

                $self->options['data']['nodes'][$this->id] = $value;
                $self->save();

            });

            Widget::setProperty('theme', function () use ($self) {

                $config  = $self->get("data.widgets.".$this->id, []);
                $default = $self->config("widget", []);

                return array_replace($default, $config);

            }, function ($value) use ($self) {

                $self->options['data']['widgets'][$this->id] = $value;
                $self->save();

            });

        },

        'view.system/site/admin/settings' => function ($event, $view) {
            $view->script('site-theme', 'theme:app/bundle/site-theme.js', 'site-settings');
            $view->data('$theme', $this);
        },

        'view.system/site/admin/edit' => function ($event, $view) {
            $view->script('site-appearance', 'theme:app/bundle/site-appearance.js', 'site-edit');
        },

        'view.system/widget/edit' => function ($event, $view) {
            $view->script('widget-appearance', 'theme:app/bundle/widget-appearance.js', 'widget-edit');
        }

    ]

];
