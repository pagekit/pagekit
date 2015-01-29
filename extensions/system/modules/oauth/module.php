<?php

use Pagekit\OAuth\Helper\OAuthHelper;

return [

    'name' => 'system/oauth',

    'main' => function ($app, $config) {

        $app['oauth'] = function() {
            return new OAuthHelper;
        };

        $app['system']->loadControllers($config);

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
