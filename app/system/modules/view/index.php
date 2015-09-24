<?php

use Pagekit\View\Event\CanonicalListener;
use Pagekit\View\Event\ResponseListener;

return [

    'name' => 'system/view',

    'main' => function ($app) {

        $app->extend('view', function ($view) use ($app) {

            $view->defer('head');
            $view->meta(['generator' => 'Pagekit']);

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

            $app->on('view.meta', function ($event, $meta) use ($app) {
                if ($meta->get('title')){
                    $title[] = $meta->get('title');
                }
                $title[] = $app->config('system/site')->get('title');
                if ($app->request()->getPathInfo() === '/') {
                    $title = array_reverse($title);
                }

                $meta->add('title', implode(' | ', $title));
            });
        },

        'site' => function ($event, $app) {
            $app->on('view.meta', function ($event, $meta) use ($app) {

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
            $styles->register('codemirror-hint', 'app/assets/codemirror/hint.css');
            $styles->register('codemirror', 'app/assets/codemirror/codemirror.css', ['codemirror-hint']);
        },

        'view.scripts' => function ($event, $scripts) use ($app) {
            $scripts->register('codemirror', 'app/assets/codemirror/codemirror.js');
            $scripts->register('jquery', 'app/assets/jquery/dist/jquery.min.js');
            $scripts->register('lodash', 'app/assets/lodash/lodash.min.js');
            $scripts->register('marked', 'app/assets/marked/marked.js');
            $scripts->register('uikit', 'app/assets/uikit/js/uikit.min.js', 'jquery');
            $scripts->register('uikit-autocomplete', 'app/assets/uikit/js/components/autocomplete.min.js', 'uikit');
            $scripts->register('uikit-datepicker', 'app/assets/uikit/js/components/datepicker.min.js', 'uikit');
            $scripts->register('uikit-form-password', 'app/assets/uikit/js/components/form-password.min.js', 'uikit');
            $scripts->register('uikit-form-select', 'app/assets/uikit/js/components/form-select.min.js', 'uikit');
            $scripts->register('uikit-htmleditor', 'app/assets/uikit/js/components/htmleditor.min.js', ['uikit', 'marked', 'codemirror']);
            $scripts->register('uikit-nestable', 'app/assets/uikit/js/components/nestable.min.js', 'uikit');
            $scripts->register('uikit-notify', 'app/assets/uikit/js/components/notify.min.js', 'uikit');
            $scripts->register('uikit-tooltip', 'app/assets/uikit/js/components/tooltip.min.js', 'uikit');
            $scripts->register('uikit-pagination', 'app/assets/uikit/js/components/pagination.min.js', 'uikit');
            $scripts->register('uikit-sortable', 'app/assets/uikit/js/components/sortable.min.js', 'uikit');
            $scripts->register('uikit-sticky', 'app/assets/uikit/js/components/sticky.min.js', 'uikit');
            $scripts->register('uikit-upload', 'app/assets/uikit/js/components/upload.min.js', 'uikit');
            $scripts->register('uikit-lightbox', 'app/assets/uikit/js/components/lightbox.min.js', 'uikit');
            $scripts->register('uikit-parallax', 'app/assets/uikit/js/components/parallax.min.js', 'uikit');
            $scripts->register('uikit-timepicker', 'app/assets/uikit/js/components/timepicker.js', 'uikit-autocomplete');
            $scripts->register('vue', 'app/system/app/bundle/vue.js', ['vue-dist', 'jquery', 'lodash', 'locale']);
            $scripts->register('vue-dist', 'app/assets/vue/dist/' . ($app->debug() ? 'vue.js' : 'vue.min.js'));
            $scripts->register('locale', $app->url('@system/intl', ['locale' => $app->module('system/intl')->getLocale()]));
        }

    ]

];
