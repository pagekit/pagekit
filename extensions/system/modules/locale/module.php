<?php

use Pagekit\Locale\Helper\CountryHelper;
use Pagekit\Locale\Helper\DateHelper;
use Pagekit\Locale\Helper\LanguageHelper;
use Pagekit\Locale\Loader\ArrayLoader;
use Pagekit\Locale\Loader\MoFileLoader;
use Pagekit\Locale\Loader\PhpFileLoader;
use Pagekit\Locale\Loader\PoFileLoader;
use Pagekit\System\Event\LocaleEvent;
use Symfony\Component\Translation\Translator;

return [

    'name' => 'system/locale',

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

            $event = $app->trigger('system.locale', new LocaleEvent);

            $app['scripts']->get('pagekit')->add(['locale' => $event->getMessages()]);
            $app['scripts']->register('locale', 'extensions/system/modules/locale/assets/js/locale.js');

        });

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

        $app->on('system.settings.edit', function ($event) use ($app) {

            $countries = $app['countries'];
            $languages = $app['languages'];
            $locales   = [];
            foreach ($app['finder']->directories()->depth(0)->in('extensions/system/languages')->name('/^[a-z]{2}(_[A-Z]{2})?$/') as $dir) {
                $code = $dir->getFileName();

                list($lang, $country) = explode('_', $code);

                $locales[$code] = $languages->isoToName($lang).' - '.$countries->isoToName($country);
            }

            $timezones = [];
            foreach (\DateTimeZone::listIdentifiers() as $timezone) {

                $parts = explode('/', $timezone);

                if (count($parts) > 2) {
                    $region = $parts[0];
                    $name = $parts[1].' - '.$parts[2];
                } elseif (count($parts) > 1) {
                    $region = $parts[0];
                    $name = $parts[1];
                } else {
                    $region = 'Other';
                    $name = $parts[0];
                }

                $timezones[$region][$timezone] = str_replace('_', ' ', $name);
            }

            $event->add('system/locale', __('Localization'), $app['view']->render('extensions/system/modules/locale/views/admin/settings.razr', ['config' => $this->config, 'locales' => $locales, 'timezones' => $timezones]));
        });

    },

    'autoload' => [

        'Pagekit\\Locale\\' => 'src'

    ],

    'config' => [

        'timezone'     => 'UTC',
        'locale'       => 'en_US',
        'locale_admin' => 'en_US'

    ]

];
