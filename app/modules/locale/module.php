<?php

use Pagekit\Locale\Helper\CountryHelper;
use Pagekit\Locale\Helper\DateHelper;
use Pagekit\Locale\Helper\LanguageHelper;
use Pagekit\Locale\Loader\ArrayLoader;
use Pagekit\Locale\Loader\MoFileLoader;
use Pagekit\Locale\Loader\PhpFileLoader;
use Pagekit\Locale\Loader\PoFileLoader;
use Symfony\Component\Translation\Translator;

return [

    'name' => 'locale',

    'main' => function ($app) {

        require __DIR__.'/src/functions.php';

        $app['translator'] = function () {

            $translator = new Translator($this->config['locale']);
            $translator->addLoader('php', new PhpFileLoader);
            $translator->addLoader('mo', new MoFileLoader);
            $translator->addLoader('po', new PoFileLoader);
            $translator->addLoader('array', new ArrayLoader);

            return $translator;
        };

        $app['languages'] = function () {
            return new LanguageHelper;
        };

        $app['countries'] = function () {
            return new CountryHelper;
        };

        $app['dates'] = function () {

            $manager = new DateHelper;
            $manager->setTimezone($this->config['timezone']);
            $manager->setFormats(array_map('__', $this->config('formats', [])));

            return $manager;
        };

        $app->on('system.init', function () use ($app) {

            $app['translator']->setLocale($locale = $this->config[$app['isAdmin'] ? 'locale_admin' : 'locale']);

            foreach ($app['module'] as $module) {

                $domains = [];

                foreach (glob($module->path.'/languages/'.$locale.'/*') ?: [] as $file) {

                    $format = substr(strrchr($file, '.'), 1);
                    $domain = basename($file, '.'.$format);

                    if (in_array($domain, $domains)) {
                        continue;
                    }

                    $domains[] = $domain;

                    $app['translator']->addResource($format, $file, $locale, $domain);
                    $app['translator']->addResource($format, $file, substr($locale, 0, 2), $domain);
                }

            }

        }, 10);

        $app->on('system.loaded', function () use ($app) {

            $app['scripts']->register('messages', $app['url']->getRoute('@system/locale', ['locale' => $app['translator']->getLocale()]));
            $app['scripts']->register('locale', 'app/modules/locale/assets/js/locale.js', ['localeConfig', 'messages']);

        });

    },

    'autoload' => [

        'Pagekit\\Locale\\' => 'src'

    ],

    'controllers' => [

        '@system: /system' => [
            'Pagekit\\Locale\\Controller\\LocaleController'
        ],

    ],

    'config' => [

        'timezone'     => 'UTC',
        'locale'       => 'en_US',
        'locale_admin' => 'en_US',
        'formats'      => [

            'none'     => '',
            'full'     => 'DATE_FULL',
            'long'     => 'DATE_LONG',
            'medium'   => 'DATE_MEDIUM',
            'short'    => 'DATE_SHORT',
            'interval' => 'DATE_INTERVAL'

        ]

    ]

];
