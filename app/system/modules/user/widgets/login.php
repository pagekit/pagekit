<?php

use Pagekit\Auth\Auth;
use Pagekit\Auth\RememberMe;

return [

    'name' => 'system/login',

    'label' => 'Login',

    'events' => [

        'view.scripts' => function ($event, $scripts) use ($app) {
            $scripts->register('widget-login', 'system/user:app/bundle/widget-login.js', ['~widgets', 'input-link']);
        }

    ],

    'render' => function ($widget) use ($app) {

        $user              = $app['user'];
        $redirect          = $widget->get('redirect_login') ?: $app['url']->current(true);
        $last_username     = $app['session']->get(Auth::LAST_USERNAME);
        $remember_me_param = RememberMe::REMEMBER_ME_PARAM;

        return $app['view']('system/user/widget-login.php', compact('widget', 'options', 'user', 'last_username', 'remember_me_param', 'redirect'));
    }

];
