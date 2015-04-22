<?php

use Pagekit\View\Asset\AssetFactory;
use Pagekit\View\Asset\AssetManager;
use Pagekit\View\Helper\DataHelper;
use Pagekit\View\Helper\DateHelper;
use Pagekit\View\Helper\DeferredHelper;
use Pagekit\View\Helper\GravatarHelper;
use Pagekit\View\Helper\MapHelper;
use Pagekit\View\Helper\MarkdownHelper;
use Pagekit\View\Helper\MetaHelper;
use Pagekit\View\Helper\PositionHelper;
use Pagekit\View\Helper\ScriptHelper;
use Pagekit\View\Helper\SectionHelper;
use Pagekit\View\Helper\StyleHelper;
use Pagekit\View\Helper\TokenHelper;
use Pagekit\View\Helper\UrlHelper;
use Pagekit\View\PhpEngine;
use Pagekit\View\ViewManager;
use Pagekit\View\ViewListener;

return [

    'name' => 'view',

    'main' => function ($app) {

        $app['view'] = function ($app) {

            $view = new ViewManager($app['events']);
            $view->addEngine(new PhpEngine());
            $view->addGlobal('app', $app);
            $view->addGlobal('view', $view);
            $view->addHelpers([
                new DataHelper($view),
                new DeferredHelper($view, $app),
                new GravatarHelper(),
                new MapHelper($view),
                new MetaHelper($view),
                new PositionHelper($view),
                new ScriptHelper($view, $app['scripts']),
                new SectionHelper($view),
                new StyleHelper($view, $app['styles']),
                new UrlHelper($app['url'])
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

            $view->on('render', function ($event, $view) use ($app) {
                if (isset($app['locator']) and $name = $app['locator']->get($view->getName())) {
                    $view->setName($name);
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

    },

    'boot' => function ($app) {

        $app->on('app.request', function () use ($app) {
            $app->subscribe(new ViewListener($app['view']));
        });

        $app->on('app.controller', function ($event) use ($app) {
            if (is_array($result = $event->getControllerResult())) {
                foreach ($result as $key => $value) {
                    if ($key === '$meta') {
                        $app['view']->meta($value);
                    } elseif ($key[0] === '$') {
                        $app['view']->data($key, $value);
                    }
                }
            }
        }, 60);

    },

    'autoload' => [

        'Pagekit\\View\\' => 'src'

    ]

];
