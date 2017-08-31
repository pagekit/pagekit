<?php

namespace Pagekit\User\Controller;

use Pagekit\Application as App;
use Pagekit\Auth\Auth;
use Pagekit\Auth\Exception\AuthException;
use Pagekit\Auth\Exception\BadCredentialsException;
use Pagekit\Session\Csrf\Exception\CsrfException;

class AuthController
{
    /**
     * @Route(defaults={"_maintenance"=true})
     * @Request({"redirect"})
     */
    public function loginAction($redirect = '')
    {
        if (!$redirect) {
            $redirect = App::url(App::config('system/user')['login_redirect']);
        }

		if (App::user()->isAuthenticated()) {
			return App::redirect($redirect);
		}

        return [
            '$view' => [
                'title' => __('Login'),
                'name' => 'system/user/login.php'
            ],
            'last_username' => App::session()->get(Auth::LAST_USERNAME),
            'redirect' => $redirect
        ];
    }

    /**
     * @Route(defaults={"_maintenance" = true})
     * @Request({"redirect": "string"})
     */
    public function logoutAction($redirect = '')
    {
        if (($event = App::auth()->logout()) && $event->hasResponse()) {
            return $event->getResponse();
        }

        return App::redirect(preg_replace('#(https?:)?//[^/]+#', '', $redirect));
    }

    /**
     * @Route(methods="POST", defaults={"_maintenance" = true})
     * @Request({"credentials": "array", "remember_me": "boolean", "redirect": "string"})
     */
    public function authenticateAction($credentials, $remember = false, $redirect = '')
    {
        try {

            if (!App::csrf()->validate()) {
                throw new CsrfException(__('Invalid token. Please try again.'));
            }

            App::auth()->authorize($user = App::auth()->authenticate($credentials, false));

            if (($event = App::auth()->login($user, $remember)) && $event->hasResponse()) {
                return $event->getResponse();
            }

            if (App::request()->isXmlHttpRequest()) {
                return App::response()->json(['csrf' => App::csrf()->generate()]);
            } else {
                return App::redirect(preg_replace('#(https?:)?//[^/]+#', '', $redirect));
            }

        } catch (CsrfException $e) {
            if (App::request()->isXmlHttpRequest()) {
                return App::response()->json(['csrf' => App::csrf()->generate()], 401);
            }
            $error = $e->getMessage();
        } catch (BadCredentialsException $e) {
            $error = __('Invalid username or password.');
        } catch (AuthException $e) {
            $error = $e->getMessage();
        }

        if (App::request()->isXmlHttpRequest()) {
            return App::response()->json($error, 401);
        } else {
            App::message()->error($error);
            return App::redirect((preg_replace('#(https?:)?//[^/]+#', '', App::url()->previous())));
        }
    }
}
