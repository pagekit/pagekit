<?php

return [

    'name' => 'system/widget-text',

    'label' => 'Text',

    'type' => 'widget',

    'render' => function ($widget) use ($app) {
        return $app['content']->applyPlugins($widget->get('content'), ['widget' => $widget, 'markdown' => $widget->get('markdown')]);
    },

    'events' => [

        'view.scripts' => function ($event, $scripts) {
            $scripts->register('widget-text', 'system/site:app/bundle/widget-text.js', '~widgets');
        }

    ]

];
