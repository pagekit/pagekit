<?php

use Pagekit\Auth\Auth;
use Pagekit\Auth\RememberMe;

return [

    'name' => 'system/widget-login',

    'label' => 'Login',

    'type' => 'widget',

    'events' => [

        'view.scripts' => function ($event, $scripts) use ($app) {
            $scripts->register('widget-login', 'system/user:app/bundle/widget-login.js', ['~widgets', 'input-link']);
        }

    ],

    'render' => function ($widget) use ($app) {

        $user = $app['user'];

        if ($user->isAuthenticated()) {
            $redirect = $widget->get('redirect_logout') ?: $app['url']->current(true);
            return $app['view']('logout', compact('widget', 'user', 'options', 'redirect'));
        }

        $redirect          = $widget->get('redirect_login') ?: $app['url']->current(true);
        $last_username     = $app['session']->get(Auth::LAST_USERNAME);
        $remember_me_param = RememberMe::REMEMBER_ME_PARAM;

        return $app['view']('system/user/widget-login.php', compact('widget', 'options', 'user', 'last_username', 'remember_me_param', 'redirect'));
    }

];
