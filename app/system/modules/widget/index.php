<?php

use Pagekit\Widget\Model\Widget;
use Pagekit\Widget\PositionHelper;
use Pagekit\Widget\PositionManager;
use Pagekit\Widget\WidgetManager;

return [

    'name' => 'system/widget',

    'main' => function ($app) {

        $app['widget'] = function ($app) {
            return new WidgetManager($app);
        };

        $app['position'] = function ($app) {

            $positions = new PositionManager($app->config($app['theme']->name));

            foreach ($app['theme']->get('positions', []) as $name => $label) {
                $positions->register($name, $label);
            }

            return $positions;
        };

        $app['module']->addLoader(function ($module) use ($app) {

            if (isset($module['widgets'])) {
                $app['widget']->register($module['widgets'], $module['path']);
            }

            return $module;
        });

    },

    'autoload' => [

        'Pagekit\\Widget\\' => 'src'

    ],

    'routes' => [

        '/site/widget' => [
            'name' => '@site/widget',
            'controller' => 'Pagekit\\Widget\\Controller\\WidgetController'
        ],
        '/api/site/widget' => [
            'name' => '@site/api/widget',
            'controller' => 'Pagekit\\Widget\\Controller\\WidgetApiController'
        ]

    ],

    'resources' => [

        'system/widget:' => '',
        'views:system/widget' => 'views'

    ],

    'permissions' => [

        'system: manage widgets' => [
            'title' => 'Manage widgets'
        ]

    ],

    'menu' => [

        'site: widgets' => [
            'label' => 'Widgets',
            'parent' => 'site',
            'url' => '@site/widget',
            'access' => 'system: manage widgets',
            'active' => '@site/widget(/edit)?',
            'priority' => 20
        ]

    ],

    'config' => [

        'widget' => [

            'positions' => [],
            'config' => [],
            'defaults' => []

        ]

    ],

    'events' => [

        'boot' => function ($event, $app) {

            Widget::defineProperty('position', function () use ($app) {
                return $app['position']->find($this->id);
            }, true);

            Widget::defineProperty('theme', function () use ($app) {

                $config  = $app['theme']->config('_widgets.'.$this->id, []);
                $default = $app['theme']->get('widget', []);

                return array_replace_recursive($default, $config);
            }, true);
        },

        'package.enable' => function ($event, $package) use ($app) {
            if ($package->getType() === 'pagekit-theme') {
                $new = $app->config($package->get('module'));
                $old = $app->config($app['theme']->name);
                $assigned = [];

                foreach ((array) $new->get('_positions') as $position => $modules) {
                    $assigned = array_merge($assigned, $modules);
                }

                foreach ((array) $old->get('_positions') as $position => $modules) {
                    foreach ((array) $modules as $module) {
                        if (!in_array($module, $assigned)) {
                            $new->push('_positions.' . $position, $module);
                        }
                    }
                }
            }
        },

        'view.init' => function ($event, $view) use ($app) {
            $view->addHelper(new PositionHelper($app['position']));
        },

        'view.scripts' => function ($event, $scripts) {
            $scripts->register('widgets', 'system/widget:app/bundle/widgets.js', 'vue');
        },

        'model.widget.init' => function ($event, $widget) use ($app) {
            if ($type = $app->widget($widget->type)) {
                $widget->data = array_replace_recursive($type->get('defaults', []), $widget->data ?: []);
            }
        },

        'model.widget.saved' => function ($event, $widget) use ($app) {
            $app['position']->assign($widget->position, $widget->id);
            $app->config($app['theme']->name)->set('_widgets.'.$widget->id, $widget->theme);
        },

        'model.role.deleted' => function ($event, $role) {
            Widget::removeRole($role);
        }

    ]

];
