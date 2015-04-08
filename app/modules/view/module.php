<?php

use Pagekit\View\PhpEngine;
use Pagekit\View\View;
use Pagekit\View\Asset\AssetFactory;
use Pagekit\View\Asset\AssetManager;
use Pagekit\View\Event\ViewListener;
use Pagekit\View\Helper\DateHelper;
use Pagekit\View\Helper\DeferredHelper;
use Pagekit\View\Helper\GravatarHelper;
use Pagekit\View\Helper\MarkdownHelper;
use Pagekit\View\Helper\TokenHelper;
use Pagekit\View\Helper\DataHelper;
use Pagekit\View\Helper\MapHelper;
use Pagekit\View\Helper\MetaHelper;
use Pagekit\View\Helper\ScriptHelper;
use Pagekit\View\Helper\SectionHelper;
use Pagekit\View\Helper\StyleHelper;
use Pagekit\View\Helper\UrlHelper;

return [

    'name' => 'view',

    'main' => function ($app) {

        $app['view'] = function ($app) {

            $view = new View();
            $view->addEngine(new PhpEngine());
            $view->addGlobal('app', $app);
            $view->addGlobal('view', $view);
            $view->addHelpers([
                new MetaHelper($view),
                new DataHelper($view),
                new MapHelper($view),
                new SectionHelper($view),
                new StyleHelper($view, $app['styles']),
                new ScriptHelper($view, $app['scripts']),
                new DeferredHelper($view, $app),
                new UrlHelper($app['url']),
                new GravatarHelper()
            ]);

            if (isset($app['csrf'])) {
                $view->addHelper(new TokenHelper($app['csrf']));
            }

            if (isset($app['dates'])) {
                $view->addHelper(new DateHelper($app['dates']));
            }

            if (isset($app['markdown'])) {
                $view->addHelper(new MarkdownHelper($app['markdown']));
            }

            $view->on('render', function($event) use ($app) {
                if (isset($app['locator']) and $template = $app['locator']->get($event->getTemplate())) {
                    $event->setTemplate($template);
                }
            }, 10);

            return $view;
        };

        $app['assets'] = function () {
            return new AssetFactory();
        };

        $app['styles'] = function ($app) {
            return new AssetManager($app['assets']);
        };

        $app['scripts'] = function ($app) {
            return new AssetManager($app['assets']);
        };

        $app->on('kernel.controller', function () use ($app) {
            $app->subscribe(new ViewListener($app['view']));
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
