<?php

use Pagekit\OAuth\OAuthHelper;

return [

    'name' => 'system/oauth',

    'main' => function ($app) {

        $app['oauth'] = function () {
            return new OAuthHelper;
        };

        $app->on('view.system:modules/settings/views/settings', function ($event, $view) use ($app) {
            $view->script('settings-oauth', 'app/system/modules/oauth/app/bundle/settings.js', 'settings');
            $view->data('$settings', [ 'options' => [ $this->name => $this->config ]]);
            $view->data('$oauth', [
                'providers' => $app['oauth']->getProvider(),
                'redirect_url' => $app['oauth']->getRedirectUrl()
            ]);
        });
    },

    'autoload' => [

        'Pagekit\\OAuth\\' => 'src'

    ],

    'routes' => [

        '@system' => [
            'path' => '/',
            'controller' => 'Pagekit\\OAuth\\Controller\\OAuthController'
        ]

    ],

    'config' => [

        'provider' => [],

    ]

];
