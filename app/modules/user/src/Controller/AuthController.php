<?php

namespace Pagekit\User\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Auth\Auth;
use Pagekit\Auth\Exception\AuthException;
use Pagekit\Auth\Exception\BadCredentialsException;
use Pagekit\Auth\RememberMe;

/**
 * @Route("/user")
 */
class AuthController extends Controller
{
    /**
     * @Route(methods="POST", defaults={"_maintenance"=true})
     * @Request({"redirect"})
     * @Response("system/user:views/login.php")
     */
    public function loginAction($redirect = '')
    {
        if (App::user()->isAuthenticated()) {
            App::message()->info(__('You are already logged in.'));
            return $this->redirect('/');
        }

        return ['head.title' => __('Login'), 'last_username' => App::session()->get(Auth::LAST_USERNAME), 'redirect' => $redirect, 'remember_me_param' => RememberMe::REMEMBER_ME_PARAM];
    }

    /**
     * @Route(defaults={"_maintenance" = true})
     */
    public function logoutAction()
    {
        return App::auth()->logout();
    }

    /**
     * @Route(methods="POST", defaults={"_maintenance" = true})
     * @Request({"credentials": "array"})
     */
    public function authenticateAction($credentials)
    {
        try {

            if (!App::csrf()->validate(App::request()->request->get('_csrf'))) {
                throw new AuthException(__('Invalid token. Please try again.'));
            }

            App::auth()->authorize($user = App::auth()->authenticate($credentials, false));

            return App::auth()->login($user);

        } catch (BadCredentialsException $e) {
            App::message()->error(__('Invalid username or password.'));
        } catch (AuthException $e) {
            App::message()->error($e->getMessage());
        }

        return $this->redirect(App::url()->previous());
    }
}
