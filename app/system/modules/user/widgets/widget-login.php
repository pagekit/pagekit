<?php

use Pagekit\Auth\Auth;
use Pagekit\Auth\RememberMe;

return [

    'name' => 'system/widget-login',

    'type' => 'widget',

    'views' => [
        'login' => 'system/user:views/site/widget-login.php',
        'logout' => 'system/user:views/site/widget-logout.php'
    ],

    'events' => [

        'view.layout' => function () use ($app) {
            $app['scripts']->register('widget-login', 'system/user:app/bundle/widget-login.js', '~widgets');
        }

    ],

    'render' => function ($widget) use ($app) {

        $user = $app['user'];

        if ($user->isAuthenticated()) {
            $redirect = $widget->get('redirect.logout') ?: $app['url']->current(true);
            return $app['view']('logout', compact('widget', 'user', 'options', 'redirect'));
        }

        $redirect          = $widget->get('redirect.login') ?: $app['url']->current(true);
        $last_username     = $app['session']->get(Auth::LAST_USERNAME);
        $remember_me_param = RememberMe::REMEMBER_ME_PARAM;

        return $app['view']('login', compact('widget', 'options', 'user', 'last_username', 'remember_me_param', 'redirect'));
    }

];
