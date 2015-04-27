<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;
use Pagekit\Auth\Auth;
use Pagekit\Auth\RememberMe;

/**
 * @Route("/")
 */
class AdminController
{
    /**
     * @Access(admin=true)
     */
    public function indexAction()
    {
        return App::redirect('@dashboard');
    }

    /**
     * @Route("/admin/login", defaults={"_maintenance"=true})
     */
    public function loginAction()
    {
        if (App::user()->isAuthenticated()) {
            return App::redirect('@system/admin');
        }

        return [
            '$view' => [
                'title'  => __('Login'),
                'name'   => 'system/theme:templates/login.php',
                'layout' => false
            ],
            'last_username' => App::session()->get(Auth::LAST_USERNAME),
            'redirect' => App::request()->get('redirect') ? : App::url('@system/admin', [], true),
            'remember_me_param' => RememberMe::REMEMBER_ME_PARAM
        ];
    }
}
