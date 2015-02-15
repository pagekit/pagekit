<?php

use Pagekit\OAuth\Helper\OAuthHelper;

return [

    'name' => 'system/oauth',

    'main' => function ($app) {

        $app['oauth'] = function() {
            return new OAuthHelper;
        };

        $app->on('system.tmpl', function ($event) {
            $event->register('settings.oauth', 'extensions/system/modules/oauth/views/tmpl/settings.razr');
        });


        $app->on('system.settings.edit', function ($event) use ($app) {
            $event->add('system/oauth', __('OAuth'), $app['view']->render('extensions/system/modules/oauth/views/admin/settings.razr', ['config' => $this->config]));
        });

    },

    'autoload' => [

        'Pagekit\\OAuth\\' => 'src'

    ],

    'controllers' => [

        '@system: /' => [
            'Pagekit\\OAuth\\Controller\\OAuthController'
        ]

    ]

];
