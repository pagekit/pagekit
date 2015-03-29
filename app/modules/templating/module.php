<?php

use Pagekit\Templating\PhpEngine;
use Pagekit\Templating\Section\DelayedRenderer;
use Pagekit\Templating\TemplateEngine;
use Pagekit\Templating\TemplateNameParser;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\Loader\FilesystemLoader;

return [

    'name' => 'system/templating',

    'main' => function ($app) {

        $app['tmpl'] = function($app) {

            $engine = new TemplateEngine();
            $engine->addGlobal('app', $app);
            $engine->addGlobal('view', $app['view']);
            $engine->addGlobal('url', $app['url']);
            $engine->addEngine($app['tmpl.php']);

            return $engine;
        };

        $app['tmpl.parser'] = function($app) {

            $parser = new TemplateNameParser($app['events']);
            $parser->addEngine('php', '.php');

            return $parser;
        };

        $app['tmpl.php'] = function($app) {

            $engine = new PhpEngine($app['tmpl.parser'], new FilesystemLoader([]));
            $engine->addHelpers([new SlotsHelper]);

            return $engine;
        };

        $app->extend('view', function($view, $app) {

            $view->on('view.render', function($event) use ($app) {
                $event->setOutput($app['tmpl']->render($event->getTemplate(), $event->getParameters()));
            });

            return $view;
        });

        $app->on('system.init', function() use ($app) {
            $app['sections']->addRenderer('delayed', new DelayedRenderer($app['events']));
        });

        $app->on('templating.reference', function($event) use ($app) {

            if (!isset($app['locator'])) {
                return;
            }

            $template = $event->getTemplateReference();

            if ($path = $app['locator']->get($template->get('path'))) {
                $template->set('name', $path); // php engine uses name
                $template->set('path', $path);
            }
        });
    },

    'autoload' => [

        'Pagekit\\Templating\\' => 'src'

    ]

];
