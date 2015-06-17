<?php

return [

    'name' => 'system/widget-text',

    'type' => 'widget',

    'main' => function ($app) {
        // $app['scripts']->register('widget-text', 'widget:app/bundle/widget-text.js', '~widgets');
    },

    'render' => function ($widget, $options = []) use ($app) {
        return $app['content']->applyPlugins($widget->get('content'), ['widget' => $widget, 'markdown' => $widget->get('markdown')]);
    }

];
