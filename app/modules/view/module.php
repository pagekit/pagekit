<?php

use Pagekit\View\PhpEngine;
use Pagekit\View\View;
use Pagekit\View\Asset\AssetFactory;
use Pagekit\View\Asset\AssetManager;
use Pagekit\View\Event\ResponseListener;
use Pagekit\View\Event\ViewListener;
use Pagekit\View\Helper\DateHelper;
use Pagekit\View\Helper\GravatarHelper;
use Pagekit\View\Helper\MarkdownHelper;
use Pagekit\View\Helper\TemplateHelper;
use Pagekit\View\Helper\TokenHelper;
use Pagekit\View\Helper\DataHelper;
use Pagekit\View\Helper\MetaHelper;
use Pagekit\View\Helper\ScriptHelper;
use Pagekit\View\Helper\SectionHelper;
use Pagekit\View\Helper\StyleHelper;
use Pagekit\View\Helper\UrlHelper;
use Pagekit\View\Section\DelayedRenderer;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Templating\Loader\FilesystemLoader;

return [

    'name' => 'view',

    'main' => function ($app) {

        $app['view'] = function ($app) {

            $view = new View();
            $view->addHelpers([
                'data'    => new DataHelper(),
                'meta'    => new MetaHelper(),
                'style'   => new StyleHelper($app['styles']),
                'script'  => new ScriptHelper($app['scripts']),
                'section' => $app['sections'],
                'url'     => new UrlHelper($app['url'])
            ]);

            return $view;
        };

        $app['styles'] = function () {
            return new AssetManager();
        };

        $app['scripts'] = function () {
            return new AssetManager();
        };

        $app['sections'] = function () {
            return new SectionHelper();
        };

        $app['templating'] = function() {
            return new PhpEngine(new TemplateNameParser(), new FilesystemLoader([]));
        };

        $app->extend('view', function($view, $app) {

            $view->addGlobal('app', $app);
            $view->addGlobal('view', $view);
            $view->addGlobal('url', $app['url']);

            $helpers = [
                'gravatar' => new GravatarHelper(),
                'tmpl'     => new TemplateHelper()
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

            $view->addHelpers($helpers);

            $view->on('view.render', function($event) use ($app) {
                if (isset($app['locator']) and $template = $app['locator']->get($event->getTemplate())) {
                    $event->setTemplate($template);
                }
            }, 10);

            $view->on('view.render', function($event) use ($app) {
                $event->setResult($app['templating']->render($event->getTemplate(), $event->getParameters()));
            }, -10);

            return $view;
        });

        $app->subscribe(new ResponseListener);

        $app->on('kernel.boot', function () use ($app) {

            $app->subscribe(new ViewListener($app['view']));

            $app['sections']->append('head', function () use ($app) {

                $head  = $app['view']->meta()->render();
                $head .= $app['view']->style()->render();
                $head .= $app['view']->data()->render();
                $head .= $app['view']->script()->render();

                return $head;
            });
        });

        $app->on('kernel.controller', function () use ($app) {
            foreach ($app['module'] as $module) {

                if (!isset($module->renderer)) {
                    continue;
                }

                foreach ($module->renderer as $name => $template) {
                    $app['sections']->addRenderer($name, function ($name, $value, $options = []) use ($app, $template) {
                        return $app['tmpl']->render($template, compact('name', 'value', 'options'));
                    });
                }
            }
        });

        $app->on('system.init', function() use ($app) {

            $debug = $app['module']['application']->config('debug');

            $app['styles']->register('codemirror', 'vendor/assets/codemirror/codemirror.css');
            $app['scripts']->register('codemirror', 'vendor/assets/codemirror/codemirror.js');
            $app['scripts']->register('jquery', 'vendor/assets/jquery/dist/jquery.min.js');
            $app['scripts']->register('lodash', 'vendor/assets/lodash/lodash.min.js');
            $app['scripts']->register('marked', 'vendor/assets/marked/marked.js');
            $app['scripts']->register('uikit', 'vendor/assets/uikit/js/uikit.min.js', 'jquery');
            $app['scripts']->register('uikit-autocomplete', 'vendor/assets/uikit/js/components/autocomplete.min.js', 'uikit');
            $app['scripts']->register('uikit-form-password', 'vendor/assets/uikit/js/components/form-password.min.js', 'uikit');
            $app['scripts']->register('uikit-htmleditor', 'vendor/assets/uikit/js/components/htmleditor.min.js', ['uikit', 'marked', 'codemirror']);
            $app['scripts']->register('uikit-pagination', 'vendor/assets/uikit/js/components/pagination.min.js', 'uikit');
            $app['scripts']->register('uikit-nestable', 'vendor/assets/uikit/js/components/nestable.min.js', 'uikit');
            $app['scripts']->register('uikit-notify', 'vendor/assets/uikit/js/components/notify.min.js', 'uikit');
            $app['scripts']->register('uikit-sortable', 'vendor/assets/uikit/js/components/sortable.min.js', 'uikit');
            $app['scripts']->register('uikit-sticky', 'vendor/assets/uikit/js/components/sticky.min.js', 'uikit');
            $app['scripts']->register('uikit-upload', 'vendor/assets/uikit/js/components/upload.min.js', 'uikit');
            $app['scripts']->register('uikit-datepicker', 'vendor/assets/uikit/js/components/datepicker.min.js', 'uikit');
            $app['scripts']->register('uikit-timepicker', 'vendor/assets/uikit/js/components/timepicker.js', 'uikit-autocomplete');
            $app['scripts']->register('gravatar', 'vendor/assets/gravatarjs/gravatar.js');
            $app['scripts']->register('system', 'app/modules/system/app/system.js', ['jquery', 'tmpl', 'locale']);
            $app['scripts']->register('vue', 'vendor/assets/vue/dist/'.($debug ? 'vue.js' : 'vue.min.js'));
            $app['scripts']->register('vue-system', 'app/modules/system/app/vue-system.js', ['vue-resource', 'lodash', 'locale', 'uikit-pagination']);
            $app['scripts']->register('vue-resource', 'app/modules/system/app/vue-resource.js', ['vue']);
            $app['scripts']->register('vue-validator', 'app/modules/system/app/vue-validator.js', ['vue']);

            $app['view']->data('$pagekit', ['version' => $app['version'], 'url' => $app['router']->getContext()->getBaseUrl(), 'csrf' => $app['csrf']->generate()]);

            $app['view']->section()->set('messages', function() use ($app) {
                return $app['view']->render('system: views/messages/messages.php');
            });

            $app['view']->section()->prepend('head', function () use ($app) {
                if ($templates = $app['view']->tmpl()->queued()) {
                    $app['view']->script('tmpl', $app['url']->get('@system/system/tmpls', ['templates' => implode(',', $templates)]));
                }
            });

            $app['sections']->addRenderer('delayed', new DelayedRenderer($app['events']));
        });

        $app->on('system.loaded', function () use ($app) {
            foreach ($app['module'] as $module) {
                if (isset($module->templates)) {
                    foreach ($module->templates as $name => $tmpl) {
                        $app['view']->tmpl()->register($name, $tmpl);
                    }
                }
            }
        });

        $app->on('kernel.view', function ($event) use ($app) {
            if (is_array($result = $event->getControllerResult())) {
                foreach ($result as $key => $value) {
                    if ($key === '$meta') {
                        $app['view']->meta($value);
                    } elseif ($key[0] === '$') {
                        $app['view']->data($key, $value);
                    }
                }
            }
        });

    },

    'autoload' => [

        'Pagekit\\View\\' => 'src'

    ]

];
