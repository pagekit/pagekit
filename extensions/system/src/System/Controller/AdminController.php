<?php

namespace Pagekit\System\Controller;

use Pagekit\Component\Auth\Auth;
use Pagekit\Component\Auth\RememberMe;
use Pagekit\Framework\Application as App;
use Pagekit\Framework\Controller\Controller;

/**
 * @Route("/")
 */
class AdminController extends Controller
{
    /**
     * @Access(admin=true)
     */
    public function indexAction()
    {
        return $this->redirect('@system/dashboard');
    }

    /**
     * @Route("/admin/login", defaults={"_maintenance"=true})
     * @Response("extensions/system/theme/templates/login.razr", layout=false)
     */
    public function loginAction()
    {
        if (App::user()->isAuthenticated()) {
            return $this->redirect('@system/admin');
        }

        return ['head.title' => __('Login'), 'last_username' => App::session()->get(Auth::LAST_USERNAME), 'redirect' => App::request()->get('redirect') ? : App::url()->route('@system/admin', [], true), 'remember_me_param' => RememberMe::REMEMBER_ME_PARAM];
    }
}
