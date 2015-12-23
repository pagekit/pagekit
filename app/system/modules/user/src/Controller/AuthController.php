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
     * @Request({"credentials": "array", "_remember_me": "boolean"})
     */
    public function authenticateAction($credentials, $remember = false)
    {
        $isXml = App::request()->isXmlHttpRequest();

        try {

            if (!App::csrf()->validate()) {
                throw new AuthException(__('Invalid token. Please try again.'));
            }

            App::auth()->authorize($user = App::auth()->authenticate($credentials, false));

            if (!$isXml) {
                return App::auth()->login($user, $remember);
            } else {
                App::auth()->setUser($user, $remember);
                return ['success' => true];
            }

        } catch (BadCredentialsException $e) {
            $error = __('Invalid username or password.');
        } catch (AuthException $e) {
            $error = $e->getMessage();
        }

        if (!$isXml) {
            App::message()->error($error);
            return App::redirect(App::url()->previous());
        } else {
            App::abort(400, $error);
        }
    }
}
