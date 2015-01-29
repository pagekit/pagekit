<?php

use Pagekit\Locale\Helper\CountryHelper;
use Pagekit\Locale\Helper\DateHelper;
use Pagekit\Locale\Helper\LanguageHelper;

return [

    'name' => 'system/locale',

    'main' => function ($app) {

        $app['languages'] = function() {
            return new LanguageHelper;
        };

        $app['countries'] = function() {
            return new CountryHelper;
        };

        $app['dates'] = function($app) {

            $manager = new DateHelper;
            $manager->setTimezone($app['option']->get('system:app.timezone', 'UTC'));
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

        $app->on('system.init', function() use ($app) {
            $app['translator']->setLocale($app['config']->get('app.locale'.($app['isAdmin'] ? '_admin' : '')));
        }, 10);

        $app->on('system.locale', function($event) {
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

    ]

];
