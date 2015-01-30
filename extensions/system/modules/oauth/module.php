<?php

use Pagekit\OAuth\Helper\OAuthHelper;

return [

    'name' => 'system/oauth',

    'main' => function ($app, $config) {

        $app['oauth'] = function() {
            return new OAuthHelper;
        };

        $app['system']->loadControllers($config['controllers']);

        $app->on('system.tmpl', function ($event) {
            $event->register('settings.oauth', 'extensions/system/modules/oauth/views/tmpl/settings.razr');
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
