<?php

use Pagekit\Intl\Helper\DateHelper;
use Pagekit\Intl\Intl;
use Pagekit\Intl\Loader\ArrayLoader;
use Pagekit\Intl\Loader\MoFileLoader;
use Pagekit\Intl\Loader\PhpFileLoader;
use Pagekit\Intl\Loader\PoFileLoader;
use Symfony\Component\Translation\Translator;

return [

    'name' => 'intl',

    'main' => function ($app) {

        $app['intl'] = function () {

            $intl = Intl::getInstance();
            $intl['date'] = new DateHelper();

            return $intl;
        };

        $app['intl']->setDefaultLocale($this->config['locale']);

        $app['translator'] = function ($app) {

            $locale = $app['intl']->getDefaultLocale();

            $translator = new Translator($locale);
            $translator->addLoader('php', new PhpFileLoader);
            $translator->addLoader('mo', new MoFileLoader);
            $translator->addLoader('po', new PoFileLoader);
            $translator->addLoader('array', new ArrayLoader);

            foreach ($app['module'] as $module) {

                $domains = [];
                $files   = glob($module->get('path')."/languages/{$locale}/*") ?: [];

                foreach ($files as $file) {

                    $format = substr(strrchr($file, '.'), 1);
                    $domain = basename($file, '.'.$format);

                    if (in_array($domain, $domains)) {
                        continue;
                    }

                    $domains[] = $domain;

                    $translator->addResource($format, $file, $locale, $domain);
                    $translator->addResource($format, $file, substr($locale, 0, 2), $domain);
                }
            }

            return $translator;
        };

        $app->extend('view', function ($view) use ($app) {
            return $view->addGlobal('intl', $app['intl']);
        });

        require __DIR__.'/functions.php';

    },

    'autoload' => [

        'Pagekit\\Intl\\' => 'src'

    ],

    'config' => [

        'locale' => 'en_US'

    ]

];
