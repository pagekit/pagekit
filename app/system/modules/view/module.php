<?php

use Pagekit\View\Event\CanonicalListener;
use Pagekit\View\Event\ResponseListener;
use Pagekit\View\Helper\TemplateHelper;

return [

    'name' => 'system/view',

    'main' => function ($app) {

        $app->extend('view', function ($view) use ($app) {

            $view->defer('head');
            $view->meta(['generator' => 'Pagekit '.$app['version']]);
            $view->data('$pagekit', ['url' => $app['router']->getContext()->getBaseUrl(), 'csrf' => $app['csrf']->generate()]);
            $view->addHelper(new TemplateHelper($view, $app));

            return $view;
        });

        $app->extend('assets', function ($assets) use ($app) {

            $assets->register('file', 'Pagekit\View\Asset\FileLocatorAsset');
            $assets->register('template', 'Pagekit\View\Asset\TemplateAsset');

            return $assets;
        });

        $app->extend('styles', function ($styles) use ($app) {

            $styles->register('codemirror', 'vendor/assets/codemirror/codemirror.css');

            return $styles;
        });

        $app->extend('scripts', function ($scripts) use ($app) {

            $scripts->register('codemirror', 'vendor/assets/codemirror/codemirror.js');
            $scripts->register('jquery', 'vendor/assets/jquery/dist/jquery.min.js');
            $scripts->register('lodash', 'vendor/assets/lodash/lodash.min.js');
            $scripts->register('marked', 'vendor/assets/marked/marked.js');
            $scripts->register('uikit', 'vendor/assets/uikit/js/uikit.min.js', 'jquery');
            $scripts->register('uikit-autocomplete', 'vendor/assets/uikit/js/components/autocomplete.min.js', 'uikit');
            $scripts->register('uikit-form-password', 'vendor/assets/uikit/js/components/form-password.min.js', 'uikit');
            $scripts->register('uikit-htmleditor', 'vendor/assets/uikit/js/components/htmleditor.min.js', ['uikit', 'marked', 'codemirror']);
            $scripts->register('uikit-pagination', 'vendor/assets/uikit/js/components/pagination.min.js', 'uikit');
            $scripts->register('uikit-nestable', 'vendor/assets/uikit/js/components/nestable.min.js', 'uikit');
            $scripts->register('uikit-notify', 'vendor/assets/uikit/js/components/notify.min.js', 'uikit');
            $scripts->register('uikit-sortable', 'vendor/assets/uikit/js/components/sortable.min.js', 'uikit');
            $scripts->register('uikit-sticky', 'vendor/assets/uikit/js/components/sticky.min.js', 'uikit');
            $scripts->register('uikit-upload', 'vendor/assets/uikit/js/components/upload.min.js', 'uikit');
            $scripts->register('uikit-datepicker', 'vendor/assets/uikit/js/components/datepicker.min.js', 'uikit');
            $scripts->register('uikit-timepicker', 'vendor/assets/uikit/js/components/timepicker.js', 'uikit-autocomplete');
            $scripts->register('gravatar', 'vendor/assets/gravatarjs/gravatar.js');
            $scripts->register('vue', 'vendor/assets/vue/dist/'.($app['debug'] ? 'vue.js' : 'vue.min.js'));
            $scripts->register('vue-system', 'app/system/app/vue-system.js', ['vue-resource', 'jquery', 'lodash', 'locale']);
            $scripts->register('vue-resource', 'app/system/app/vue-resource.js', 'vue');
            $scripts->register('vue-validator', 'app/system/app/vue-validator.js', 'vue');

            return $scripts;
        });

        $app->subscribe(new CanonicalListener(), new ResponseListener());

    },

    'autoload' => [

        'Pagekit\\View\\' => 'src'

    ]

];
