<?php

use Pagekit\Templating\Helper\GravatarHelper;
use Pagekit\Templating\Helper\MarkdownHelper;
use Pagekit\Templating\Helper\ScriptHelper;
use Pagekit\Templating\Helper\SectionHelper;
use Pagekit\Templating\Helper\StyleHelper;
use Pagekit\Templating\Helper\TokenHelper;
use Pagekit\Templating\Razr\Directive\SectionDirective;
use Pagekit\Templating\Razr\Directive\TransDirective;
use Pagekit\Templating\RazrEngine;
use Pagekit\Templating\Section\DelayedRenderer;
use Pagekit\Templating\TemplateNameParser;
use Razr\Directive\FunctionDirective;
use Razr\Loader\FilesystemLoader as RazrFilesystemLoader;
use Symfony\Component\Templating\DelegatingEngine;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\PhpEngine;

return [

    'name' => 'system/templating',

    'main' => function ($app, $config) {

        $app['tmpl'] = function() {
            return new DelegatingEngine;
        };

        $app['tmpl.parser'] = function($app) {

            $parser = new TemplateNameParser($app['events']);
            $parser->addEngine('php', '.php');

            return $parser;
        };

        $app['tmpl.php'] = function($app) {

            $helpers = [new SlotsHelper, new GravatarHelper];

            if (isset($app['styles'])) {
                $helpers[] = new StyleHelper($app['styles']);
            }

            if (isset($app['scripts'])) {
                $helpers[] = new ScriptHelper($app['scripts']);
            }

            if (isset($app['sections'])) {
                $helpers[] = new SectionHelper($app['sections']);
            }

            if (isset($app['csrf'])) {
                $helpers[] = new TokenHelper($app['csrf']);
            }

            if (isset($app['markdown'])) {
                $helpers[] = new MarkdownHelper($app['markdown']);
            }

            $engine = new PhpEngine($app['tmpl.parser'], new FilesystemLoader([]));
            $engine->addHelpers($helpers);

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

            if (isset($app['styles'])) {
                $engine->addDirective(new FunctionDirective('style', function($name, $asset = null, $dependencies = [], $options = []) use ($app) {
                    $app['styles']->queue($name, $asset, $dependencies, $options);
                }));
            }

            if (isset($app['scripts'])) {
                $engine->addDirective(new FunctionDirective('script', function($name, $asset = null, $dependencies = [], $options = []) use ($app) {
                    $app['scripts']->queue($name, $asset, $dependencies, $options);
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

            return $engine;
        };

        $app->on('system.init', function() use ($app) {
            $app['tmpl']->addEngine($app['tmpl.php']);
            $app['tmpl']->addEngine($app['tmpl.razr']);

            $app['sections']->addRenderer('delayed', new DelayedRenderer($app['events']));
        });
    },

    'autoload' => [

        'Pagekit\\Templating\\' => 'src'

    ]

];
