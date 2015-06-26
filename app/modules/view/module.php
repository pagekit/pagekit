<?php

use Pagekit\View\Asset\AssetFactory;
use Pagekit\View\Asset\AssetManager;
use Pagekit\View\Helper\DataHelper;
use Pagekit\View\Helper\DeferredHelper;
use Pagekit\View\Helper\GravatarHelper;
use Pagekit\View\Helper\MapHelper;
use Pagekit\View\Helper\MarkdownHelper;
use Pagekit\View\Helper\MetaHelper;
use Pagekit\View\Helper\ScriptHelper;
use Pagekit\View\Helper\SectionHelper;
use Pagekit\View\Helper\StyleHelper;
use Pagekit\View\Helper\TokenHelper;
use Pagekit\View\Helper\UrlHelper;
use Pagekit\View\PhpEngine;
use Pagekit\View\View;
use Pagekit\View\ViewListener;

return [

    'name' => 'view',

    'main' => function ($app) {

        $app['view'] = function ($app) {

            $view = new View($app['events']);
            $view->addEngine(new PhpEngine());
            $view->addGlobal('app', $app);
            $view->addGlobal('view', $view);
            $view->addHelpers([
                new DataHelper(),
                new DeferredHelper($app['events']),
                new GravatarHelper(),
                new MapHelper(),
                new MetaHelper(),
                new ScriptHelper($app['scripts']),
                new SectionHelper(),
                new StyleHelper($app['styles']),
                new UrlHelper($app['url'])
            ]);

            if (isset($app['csrf'])) {
                $view->addHelper(new TokenHelper($app['csrf']));
            }

            if (isset($app['markdown'])) {
                $view->addHelper(new MarkdownHelper($app['markdown']));
            }

            $view->on('render', function ($event) use ($app) {
                if (isset($app['locator']) and $name = $app['locator']->get($event->getTemplate())) {
                    $event->setTemplate($name);
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

        $app['module']->addLoader(function ($name, $module) use ($app) {

            if (isset($module['views'])) {
                foreach ((array) $module['views'] as $name => $view) {
                    $app['view']->map($name, $view);
                }
            }

            return $module;
        });

    },

    'events' => [

        'request' => function () use ($app) {
            $app->subscribe(new ViewListener($app['view']));
        }

    ],

    'autoload' => [

        'Pagekit\\View\\' => 'src'

    ]

];
