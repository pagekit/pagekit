<?php

use Pagekit\View\Event\CanonicalListener;
use Pagekit\View\Event\ResponseListener;
use Pagekit\View\Helper\EditorHelper;
use Pagekit\View\Helper\FinderHelper;
use Pagekit\View\Helper\PositionHelper;
use Pagekit\View\Helper\TemplateHelper;

return [

    'name' => 'system/view',

    'main' => function ($app) {

        $app->extend('view', function ($view) use ($app) {

            $view->defer('head');
            $view->meta(['generator' => 'Pagekit '.$app['version']]);
            $view->addHelper(new EditorHelper());
            $view->addHelper(new FinderHelper());
            $view->addHelper(new PositionHelper());
            $view->addHelper(new TemplateHelper($app['scripts']));

            return $view;
        });

        $app->extend('assets', function ($assets) use ($app) {

            $assets->register('file', 'Pagekit\View\Asset\FileLocatorAsset');
            $assets->register('template', 'Pagekit\View\Asset\TemplateAsset');

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

        'site' => function($event) use ($app) {
            $app->subscribe(new CanonicalListener());
        },

        'view.head' => [function ($event, $view) use ($app) {

            $view->data('$pagekit', ['url' => $app['router']->getContext()->getBaseUrl(), 'csrf' => $app['csrf']->generate()]);

            $app['styles']->register('codemirror', 'vendor/assets/codemirror/codemirror.css');
            $app['scripts']->register('codemirror', 'vendor/assets/codemirror/codemirror.js');
            $app['scripts']->register('jquery', 'vendor/assets/jquery/dist/jquery.min.js');
            $app['scripts']->register('lodash', 'vendor/assets/lodash/lodash.min.js');
            $app['scripts']->register('marked', 'vendor/assets/marked/marked.js');
            $app['scripts']->register('uikit', 'vendor/assets/uikit/js/uikit.min.js', 'jquery');
            $app['scripts']->register('uikit-autocomplete', 'vendor/assets/uikit/js/components/autocomplete.min.js', 'uikit');
            $app['scripts']->register('uikit-datepicker', 'vendor/assets/uikit/js/components/datepicker.min.js', 'uikit');
            $app['scripts']->register('uikit-form-password', 'vendor/assets/uikit/js/components/form-password.min.js', 'uikit');
            $app['scripts']->register('uikit-form-select', 'vendor/assets/uikit/js/components/form-select.min.js', 'uikit');
            $app['scripts']->register('uikit-htmleditor', 'vendor/assets/uikit/js/components/htmleditor.min.js', ['uikit', 'marked', 'codemirror']);
            $app['scripts']->register('uikit-nestable', 'vendor/assets/uikit/js/components/nestable.min.js', 'uikit');
            $app['scripts']->register('uikit-notify', 'vendor/assets/uikit/js/components/notify.min.js', 'uikit');
            $app['scripts']->register('uikit-tooltip', 'vendor/assets/uikit/js/components/tooltip.min.js', 'uikit');
            $app['scripts']->register('uikit-pagination', 'vendor/assets/uikit/js/components/pagination.min.js', 'uikit');
            $app['scripts']->register('uikit-sortable', 'vendor/assets/uikit/js/components/sortable.min.js', 'uikit');
            $app['scripts']->register('uikit-sticky', 'vendor/assets/uikit/js/components/sticky.min.js', 'uikit');
            $app['scripts']->register('uikit-upload', 'vendor/assets/uikit/js/components/upload.min.js', 'uikit');
            $app['scripts']->register('uikit-timepicker', 'vendor/assets/uikit/js/components/timepicker.js', 'uikit-autocomplete');
            $app['scripts']->register('vue', 'app/system/app/bundle/vue.js', ['vue-dist', 'jquery', 'lodash', 'globalize']);
            $app['scripts']->register('vue-dist', 'vendor/assets/vue/dist/'.($app['debug'] ? 'vue.js' : 'vue.min.js'));
            $app['scripts']->register('v-imagepicker', 'app/system/app/bundle/imagepicker.js', ['vue', 'finder']);
            $app['scripts']->register('globalize', 'app/system/app/bundle/globalize.js', 'globalize-data');
            $app['scripts']->register('globalize-data', $app['url']->getRoute('@system/intl', ['locale' => $app['intl']->getDefaultLocale()]));

        }, 50]

    ]

];
