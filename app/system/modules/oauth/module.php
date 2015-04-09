<?php

use Pagekit\OAuth\OAuthHelper;

return [

    'name' => 'system/oauth',

    'main' => function ($app) {

        $app['oauth'] = function () {
            return new OAuthHelper;
        };

        $app->on('system.settings.edit', function ($event) use ($app) {

            $app['view']->script('settings-oauth', 'app/system/modules/oauth/app/settings.js', 'settings');

            $event->options($this->name, $this->config);
            $event->data('oauth', $app['oauth']->getProvider());
            $event->data('redirect_url', $app['oauth']->getRedirectUrl());
            $event->section($this->name, 'OAuth', 'app/system/modules/oauth/views/settings.php');
        });
    },

    'autoload' => [

        'Pagekit\\OAuth\\' => 'src'

    ],

    'controllers' => [

        '@system: /' => [
            'Pagekit\\OAuth\\Controller\\OAuthController'
        ]

    ],

    'config' => [
        'provider' => [],
    ]

];
