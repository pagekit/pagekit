<?php

use Pagekit\OAuth\Helper\OAuthHelper;

return [

    'name' => 'oauth',

    'main' => function ($app) {

        $app['oauth'] = function () {
            return new OAuthHelper;
        };

        $app->on('system.settings.edit', function ($event) use ($app) {
            $app['view']->script('oauth-settings', 'app/modules/oauth/assets/js/settings.js', 'vue-system');

            $event->options($this->name, $this->config);
            $event->data('oauth', $app['oauth']->getProvider());
            $event->data('redirect_url', $app['oauth']->getRedirectUrl());
            $event->section($this->name, 'OAuth', 'app/modules/oauth/views/admin/settings.php');
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
