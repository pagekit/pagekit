<?php

use Pagekit\View\ViewListener;
use Pagekit\View\Event\ResponseListener;
use Pagekit\View\Helper\DateHelper;
use Pagekit\View\Helper\GravatarHelper;
use Pagekit\View\Helper\MarkdownHelper;
use Pagekit\View\Helper\TokenHelper;

return [

    'name' => 'system/view',

    'main' => function ($app) {

        $app->extend('view', function($view, $app) {

            $helpers = [
                'gravatar' => new GravatarHelper(),
            ];

            if (isset($app['dates'])) {
                $helpers['date'] = new DateHelper($app['dates']);
            }

            if (isset($app['markdown'])) {
                $helpers['markdown'] = new MarkdownHelper($app['markdown']);
            }

            if (isset($app['csrf'])) {
                $helpers['token'] = new TokenHelper($app['csrf']);
            }

            return $view->addHelpers($helpers);
        });

        $app->subscribe(new ResponseListener);

        $app->on('system.init', function() use ($app) {

            $debug = $app['module']['framework']->config('debug');

            $app['scripts']->register('angular', 'vendor/assets/angular/angular.min.js', 'jquery');
            $app['scripts']->register('angular-animate', 'vendor/assets/angular-animate/angular-animate.min.js', 'angular');
            $app['scripts']->register('angular-cookies', 'vendor/assets/angular-cookies/angular-cookies.min.js', 'angular');
            $app['scripts']->register('angular-loader', 'vendor/assets/angular-loader/angular-loader.min.js', 'angular');
            $app['scripts']->register('angular-messages', 'vendor/assets/angular-messages/angular-messages.min.js', 'angular');
            $app['scripts']->register('angular-resource', 'vendor/assets/angular-resource/angular-resource.min.js', 'angular');
            $app['scripts']->register('angular-route', 'vendor/assets/angular-route/angular-route.min.js', 'angular');
            $app['scripts']->register('angular-sanitize', 'vendor/assets/angular-sanitize/angular-sanitize.min.js', 'angular');
            $app['scripts']->register('angular-touch', 'vendor/assets/angular-touch/angular-touch.min.js', 'angular');
            $app['scripts']->register('application', 'extensions/system/app/application.js', 'angular');
            $app['scripts']->register('application-directives', 'extensions/system/app/directives.js', 'application');
            $app['scripts']->register('jquery', 'vendor/assets/jquery/dist/jquery.min.js', [], ['requirejs' => true]);
            $app['scripts']->register('requirejs', 'extensions/system/assets/js/require.min.js', 'requirejs-config');
            $app['scripts']->register('requirejs-config', 'extensions/system/assets/js/require.js', 'pagekit');
            $app['scripts']->register('uikit', 'vendor/assets/uikit/js/uikit.min.js', 'jquery', ['requirejs' => true]);
            $app['scripts']->register('uikit-autocomplete', 'vendor/assets/uikit/js/components/autocomplete.min.js', 'uikit', ['requirejs' => true]);
            $app['scripts']->register('uikit-form-password', 'vendor/assets/uikit/js/components/form-password.min.js', 'uikit', ['requirejs' => true]);
            $app['scripts']->register('uikit-nestable', 'vendor/assets/uikit/js/components/nestable.min.js', 'uikit', ['requirejs' => true]);
            $app['scripts']->register('uikit-notify', 'vendor/assets/uikit/js/components/notify.min.js', 'uikit', ['requirejs' => true]);
            $app['scripts']->register('uikit-pagination', 'vendor/assets/uikit/js/components/pagination.min.js', 'uikit');
            $app['scripts']->register('uikit-sortable', 'vendor/assets/uikit/js/components/sortable.min.js', 'uikit', ['requirejs' => true]);
            $app['scripts']->register('uikit-sticky', 'vendor/assets/uikit/js/components/sticky.min.js', 'uikit', ['requirejs' => true]);
            $app['scripts']->register('uikit-upload', 'vendor/assets/uikit/js/components/upload.min.js', 'uikit');
            $app['scripts']->register('gravatar', 'vendor/assets/gravatarjs/gravatar.js');
            $app['scripts']->register('system', 'extensions/system/app/system.js', ['jquery', 'locale']);
            $app['scripts']->register('vue', 'vendor/assets/vue/dist/'.($debug ? 'vue.js' : 'vue.min.js'));
            $app['scripts']->register('vue-system', 'extensions/system/app/vue-system.js', ['vue', 'system', 'uikit-pagination']);
            $app['scripts']->register('vue-validator', 'extensions/system/app/vue-validator.js', ['vue']);

            $app['view']->data('pagekit', ['version' => $app['version'], 'url' => $app['router']->getContext()->getBaseUrl(), 'csrf' => $app['csrf']->generate()]);
            $app['view']->section()->set('messages', function() use ($app) {
                return $app['tmpl']->render('extensions/system/views/messages/messages.php');
            });

        });

    },

    'autoload' => [

        'Pagekit\\View\\' => 'src'

    ]

];
