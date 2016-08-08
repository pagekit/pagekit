<?php

use Pagekit\Twig\TwigCache;
use Pagekit\Twig\TwigLoader;
use Pagekit\View\Loader\FilesystemLoader;
use Symfony\Component\Templating\Loader\FilesystemLoader as SymfonyFilesystemLoader;

return [

    'name' => 'view/twig',

    'main' => function ($app) {

        $app['twig'] = function ($app) {

            $twig = new Twig_Environment(new TwigLoader(isset($app['locator']) ? new FilesystemLoader($app['locator']) : new SymfonyFilesystemLoader([])), [
                'cache' => new TwigCache($app['path.cache']),
                'auto_reload' => true,
                'debug' => $app['debug'],
            ]);

            if (isset($app['debug']) && $app['debug']) {
                $twig->addExtension(new Twig_Extension_Debug());
            }

            return $twig;

         };

    },

    'autoload' => [

        'Pagekit\\Twig\\' => 'src'

    ]

];
