<?php

use Pagekit\System\OAuthHelper;

return [

    'name' => 'system/oauth',

    'main' => function ($app) {

        $app['oauth'] = function () {
            return new OAuthHelper;
        };

        $app->on('system.settings.edit', function ($event) use ($app) {

            $app['view']->script('settings-oauth', 'app/system/modules/oauth/app/oauth.js', 'settings');

            $event->options($this->name, $this->config);
            $event->data('oauth', $app['oauth']->getProvider());
            $event->data('redirect_url', $app['oauth']->getRedirectUrl());
            $event->section($this->name, 'OAuth', 'app/system/modules/oauth/views/oauth.php');
        });
    },

    'autoload' => [

        'Pagekit\\System\\' => 'src'

    ],

    'controllers' => [

        '@system: /' => [
            'Pagekit\\System\\Controller\\OAuthController'
        ]

    ],

    'config' => [
        'provider' => [],
    ]

];
