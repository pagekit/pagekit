<?php

use Pagekit\Templating\RazrEngine;
use Pagekit\Templating\PhpEngine;
use Pagekit\Templating\TemplateEngine;
use Pagekit\Templating\TemplateNameParser;
use Pagekit\Templating\Helper\DateHelper;
use Pagekit\Templating\Helper\FinderHelper;
use Pagekit\Templating\Helper\GravatarHelper;
use Pagekit\Templating\Helper\TokenHelper;
use Pagekit\Templating\Razr\Directive\SectionDirective;
use Pagekit\Templating\Razr\Directive\TransDirective;
use Pagekit\Templating\Section\DelayedRenderer;
use Razr\Directive\FunctionDirective;
use Razr\Loader\FilesystemLoader as RazrFilesystemLoader;
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
            $engine->addEngine($app['tmpl.razr']);

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


        $app['tmpl.razr'] = function($app) {

            $parser = $app['tmpl.parser'];
            $parser->addEngine('razr', '.razr');

            $engine = new RazrEngine($parser, new RazrFilesystemLoader, $app['path'].'/app/cache/templates');
            $engine->addDirective(new FunctionDirective('gravatar', [new GravatarHelper, 'get']));
            $engine->addGlobal('app', $app);

            $engine->addDirective(new FunctionDirective('url', [$app['url'], 'get']));
            $engine->addFunction('url', [$app['url'], 'get']);

            $engine->addDirective(new FunctionDirective('static_url', [$app['url'], 'getStatic']));
            $engine->addFunction('static_url', [$app['url'], 'getStatic']);

            $engine->addDirective(new FunctionDirective('finder', [new FinderHelper, 'render']));

            if (isset($app['styles'])) {
                $engine->addDirective(new FunctionDirective('style', function($name, $asset = null, $dependencies = [], $options = []) use ($app) {
                    $app['styles']->add($name, $asset, $dependencies, $options);
                }));
            }

            if (isset($app['scripts'])) {
                $engine->addDirective(new FunctionDirective('script', function($name, $asset = null, $dependencies = [], $options = []) use ($app) {
                    $app['scripts']->add($name, $asset, $dependencies, $options);
                }));
            }

            if (isset($app['sections'])) {
                $engine->addDirective(new SectionDirective);
                $engine->addFunction('hasSection', [$app['sections'], 'has']);
            }

            if (isset($app['csrf'])) {
                $engine->addDirective(new FunctionDirective('token', [new TokenHelper($app['csrf']), 'generate']));
            }

            if (isset($app['markdown'])) {
                $engine->addDirective(new FunctionDirective('markdown', [$app['markdown'], 'parse']));
            }

            if (isset($app['translator'])) {
                $engine->addDirective(new TransDirective);
            }

            if (isset($app['dates'])) {
                $helper = new DateHelper($app['dates']);
                $engine->addDirective(new FunctionDirective('date', [$helper, 'format']));
                $engine->addFunction('date', [$helper, 'format']);
            }

            return $engine;
        };

        $app->on('system.init', function() use ($app) {
            $app['sections']->addRenderer('delayed', new DelayedRenderer($app['events']));
        });

        $app->on('view.render', function($event) use ($app) {
            $event->setOutput($app['tmpl']->render($event->getTemplate(), $event->getParameters()));
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
