<?php

use Pagekit\Locale\Helper\CountryHelper;
use Pagekit\Locale\Helper\DateHelper;
use Pagekit\Locale\Helper\LanguageHelper;
use Pagekit\Locale\Loader\MoFileLoader;
use Pagekit\Locale\Loader\PhpFileLoader;
use Pagekit\Locale\Loader\PoFileLoader;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;

return [

    'name' => 'system/locale',

    'main' => function ($app, $config) {

        require __DIR__.'/src/functions.php';

        $app['translator'] = function () use ($config) {

            $translator = new Translator($config['locale']);
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

        $app['dates'] = function () use ($config) {

            $manager = new DateHelper;
            $manager->setTimezone($config['timezone']);
            $manager->setFormats([
                DateHelper::NONE      => '',
                DateHelper::FULL      => __('DATE_FULL'),
                DateHelper::LONG      => __('DATE_LONG'),
                DateHelper::MEDIUM    => __('DATE_MEDIUM'),
                DateHelper::SHORT     => __('DATE_SHORT'),
                DateHelper::INTERVAL  => __('DATE_INTERVAL')
            ]);

            return $manager;
        };

        $app->on('system.init', function () use ($app, $config) {

            $app['translator']->setLocale($locale = $config[$app['isAdmin'] ? 'locale_admin' : 'locale']);

            foreach ($app['module']->getConfigs() as $config) {

                $domains = [];

                foreach (glob($config['path'].'/languages/'.$locale.'/*') ?: [] as $file) {

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

        $app->on('system.locale', function ($event) {
            $event->addMessages([

                'short'       => __('DATE_SHORT'),
                'medium'      => __('DATE_MEDIUM'),
                'long'        => __('DATE_LONG'),
                'full'        => __('DATE_FULL'),
                'shortdays'   => [__('Mon'), __('Tue'), __('Wed'), __('Thu'), __('Fri'), __('Sat'), __('Sun')],
                'longdays'    => [__('Monday'), __('Tuesday'), __('Wednesday'), __('Thursday'), __('Friday'), __('Saturday'), __('Sunday')],
                'shortmonths' => [__('Jan'), __('Feb'), __('Mar'), __('Apr'), __('May'), __('Jun'), __('Jul'), __('Aug'), __('Sep'), __('Oct'), __('Nov'), __('Dec')],
                'longmonths'  => [__('January'), __('February'), __('March'), __('April'), __('May'), __('June'), __('July'), __('August'), __('September'), __('October'), __('November'), __('December')]

            ], 'date');
        });

    },

    'autoload' => [

        'Pagekit\\Locale\\' => 'src'

    ],

    'timezone'     => 'UTC',
    'locale'       => 'en_US',
    'locale_admin' => 'en_US'

];
