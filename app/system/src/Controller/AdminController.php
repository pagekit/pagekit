<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Auth\Auth;
use Pagekit\Auth\RememberMe;

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
        return $this->redirect('@dashboard');
    }

    /**
     * @Route("/admin/login", defaults={"_maintenance"=true})
     * @Response("app/system/modules/theme/templates/login.php", layout=false)
     */
    public function loginAction()
    {
        if (App::user()->isAuthenticated()) {
            return $this->redirect('@system/admin');
        }

        return ['head.title' => __('Login'), 'last_username' => App::session()->get(Auth::LAST_USERNAME), 'redirect' => App::request()->get('redirect') ? : App::url('@system/admin', [], true), 'remember_me_param' => RememberMe::REMEMBER_ME_PARAM];
    }
}
