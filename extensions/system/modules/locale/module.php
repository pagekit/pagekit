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

    },

    'autoload' => [

        'Pagekit\\Locale\\' => 'src'

    ]

];
