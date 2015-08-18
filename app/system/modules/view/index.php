<?php

use Pagekit\View\Event\CanonicalListener;
use Pagekit\View\Event\ResponseListener;

return [

    'name' => 'system/view',

    'main' => function ($app) {

        $app->extend('view', function ($view) use ($app) {

            $view->defer('head');
            $view->meta(['generator' => 'Pagekit '.$app['version']]);

            return $view;
        });

        $app->extend('assets', function ($assets) use ($app) {

            $assets->register('file', 'Pagekit\View\Asset\FileLocatorAsset');

            return $assets;
        });

    },

    'autoload' => [

        'Pagekit\\View\\' => 'src'

    ],

    'events' => [

        'boot' => function ($event, $app) {
            $app->subscribe(new ResponseListener());
        },

        'site' => function($event, $app) {
            $app->on('view.meta', function($event, $meta) use ($app) {

                $route = $app['url']->get(
                    $app['request']->attributes->get('_route'),
                    $app['request']->attributes->get('_route_params', [])
                );

                if ($route != $app['request']->getRequestUri()) {
                    $meta->add('canonical', $route);
                }

            });
        },

        'view.data' => function ($event, $data) use ($app) {
            $data->add('$pagekit', [
                'url' => $app['router']->getContext()->getBaseUrl(),
                'csrf' => $app['csrf']->generate()
            ]);
        },

        'view.styles' => function ($event, $styles) {
            $styles->register('codemirror-hint', 'vendor/assets/codemirror/hint.css');
            $styles->register('codemirror', 'vendor/assets/codemirror/codemirror.css', ['codemirror-hint']);
        },

        'view.scripts' => function ($event, $scripts) use ($app) {
            $scripts->register('codemirror', 'vendor/assets/codemirror/codemirror.js');
            $scripts->register('jquery', 'vendor/assets/jquery/dist/jquery.min.js');
            $scripts->register('lodash', 'vendor/assets/lodash/lodash.min.js');
            $scripts->register('marked', 'vendor/assets/marked/marked.js');
            $scripts->register('uikit', 'vendor/assets/uikit/js/uikit.min.js', 'jquery');
            $scripts->register('uikit-autocomplete', 'vendor/assets/uikit/js/components/autocomplete.min.js', 'uikit');
            $scripts->register('uikit-datepicker', 'vendor/assets/uikit/js/components/datepicker.min.js', 'uikit');
            $scripts->register('uikit-form-password', 'vendor/assets/uikit/js/components/form-password.min.js', 'uikit');
            $scripts->register('uikit-form-select', 'vendor/assets/uikit/js/components/form-select.min.js', 'uikit');
            $scripts->register('uikit-htmleditor', 'vendor/assets/uikit/js/components/htmleditor.min.js', ['uikit', 'marked', 'codemirror']);
            $scripts->register('uikit-nestable', 'vendor/assets/uikit/js/components/nestable.min.js', 'uikit');
            $scripts->register('uikit-notify', 'vendor/assets/uikit/js/components/notify.min.js', 'uikit');
            $scripts->register('uikit-tooltip', 'vendor/assets/uikit/js/components/tooltip.min.js', 'uikit');
            $scripts->register('uikit-pagination', 'vendor/assets/uikit/js/components/pagination.min.js', 'uikit');
            $scripts->register('uikit-sortable', 'vendor/assets/uikit/js/components/sortable.min.js', 'uikit');
            $scripts->register('uikit-sticky', 'vendor/assets/uikit/js/components/sticky.min.js', 'uikit');
            $scripts->register('uikit-upload', 'vendor/assets/uikit/js/components/upload.min.js', 'uikit');
            $scripts->register('uikit-timepicker', 'vendor/assets/uikit/js/components/timepicker.js', 'uikit-autocomplete');
            $scripts->register('vue', 'app/system/app/bundle/vue.js', ['vue-dist', 'jquery', 'lodash', 'locale']);
            $scripts->register('vue-dist', 'vendor/assets/vue/dist/'.($app->debug() ? 'vue.js' : 'vue.min.js'));
            $scripts->register('locale', $app->url('@system/intl', ['locale' => $app->module('system/intl')->getLocale()]));
        }

    ]

];
