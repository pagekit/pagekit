<?php

use Pagekit\Templating\PhpEngine;
use Pagekit\Templating\Section\DelayedRenderer;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\Loader\FilesystemLoader;

return [

    'name' => 'system/templating',

    'main' => function ($app) {

        $app['templating'] = function($app) {

            $engine = new PhpEngine(new TemplateNameParser(), new FilesystemLoader([]));
            $engine->addHelpers([new SlotsHelper]);

            return $engine;
        };

        $app->extend('view', function($view, $app) {

            $view->on('view.render', function($event) use ($app) {
                if (isset($app['locator']) and $template = $app['locator']->get($event->getTemplate())) {
                    $event->setTemplate($template);
                }
            });

            $view->on('view.render', function($event) use ($app) {
                $event->setResult($app['templating']->render($event->getTemplate(), $event->getParameters()));
            }, -10);

            return $view;
        });

        $app->on('system.init', function() use ($app) {
            $app['sections']->addRenderer('delayed', new DelayedRenderer($app['events']));
        });

    },

    'autoload' => [

        'Pagekit\\Templating\\' => 'src'

    ]

];
