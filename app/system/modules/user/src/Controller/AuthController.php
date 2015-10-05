<?php

namespace Pagekit\User\Controller;

use Pagekit\Application as App;
use Pagekit\Auth\Auth;
use Pagekit\Auth\Exception\AuthException;
use Pagekit\Auth\Exception\BadCredentialsException;

class AuthController
{
    /**
     * @Route(defaults={"_maintenance"=true})
     * @Request({"redirect"})
     */
    public function loginAction($redirect = '')
    {
        if (App::user()->isAuthenticated()) {
            App::message()->info(__('You are already logged in.'));
            return App::redirect();
        }

        return [
            '$view' => [
                'title' => __('Login'),
                'name'  => 'system/user/login.php'
            ],
            'last_username' => App::session()->get(Auth::LAST_USERNAME),
            'remember_me_param' => Auth::REMEMBER_ME_PARAM,
            'redirect' => $redirect
        ];
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

            if (!App::csrf()->validate()) {
                throw new AuthException(__('Invalid token. Please try again.'));
            }

            App::auth()->authorize($user = App::auth()->authenticate($credentials, false));

            return App::auth()->login($user, App::request()->get(Auth::REMEMBER_ME_PARAM));

        } catch (BadCredentialsException $e) {
            App::message()->error(__('Invalid username or password.'));
        } catch (AuthException $e) {
            App::message()->error($e->getMessage());
        }

        return App::redirect(App::url()->previous());
    }
}
