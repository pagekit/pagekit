<?php

use Pagekit\Twig\TwigCache;
use Pagekit\Twig\TwigLoader;
use Pagekit\View\Loader\FilesystemLoader;
use Symfony\Component\Templating\Loader\FilesystemLoader as SymfonyFilesystemLoader;

return [

    'name' => 'view/twig',

    'main' => function ($app) {

        $app['twig'] = function ($app) {

            return new Twig_Environment(new TwigLoader(isset($app['locator']) ? new FilesystemLoader($app['locator']) : new SymfonyFilesystemLoader([])), [
                'cache' => new TwigCache($app['path.cache']),
                'auto_reload' => true
            ]);

        };

    },

    'autoload' => [

        'Pagekit\\Twig\\' => 'src'

    ]

];
