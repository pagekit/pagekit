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

        if (App::user()->isAuthenticated()) {
            $module = App::module('system/user');
            $url = App::url($module->config['login_redirect']);
            return App::redirect($url);
        }

        return self::loginView([
            'last_username' => App::session()->get(Auth::LAST_USERNAME),
            'redirect' => $redirect
        ]);

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
            App::abort(401, $error);
        } else {
            App::message()->error($error);
            return App::redirect((preg_replace('#(https?:)?//[^/]+#', '', App::url()->previous())));
        }
    }

    /**
     * Returns a renderable view configuration for a login form
     *
     * @param array $args Login form arguments
     *          $args['title'] : The page title
     *          $args['last_username'] : Username to pre-fill in login form
     *          $args['redirect'] : Path to redirect after successful login
     *          $args['message'] : Overwrites default login box headline
     * @return array View config that can be rendered, i.e. by returning from a controller action
     */
    public static function loginView($args = [])
    {
        $args = array_merge([
            'title' => __('Login'),
            'last_username' => '',
            'redirect' => '',
            'message' => __('Sign in to your account')
        ], $args);

        return [
            '$view' => [
                'title' => $args['title'],
                'name' => 'system/user/login.php'
            ],
            'last_username' => $args['last_username'],
            'redirect' => $args['redirect'],
            'message' => $args['message']
        ];
    }

    /**
     * Returns a renderable view configuration for a generic message view
     * @param array $args
     *          $args['title'] : The page title
     *          $args['message'] : The message to display
     *          $args['success'] : Is this a success message [default: true]
     *          $args['link'] : Optional button link to display [default: '']
     *          $args['label'] : Optional button label ['default: '']
     * @return array
     */
    public static function messageView($args = [])
    {
        $args = array_merge([
            'title' => __('Account activation'),
            'message' => '',
            'success' => true,
            'link' => '',
            'label' => ''
        ], $args);

        return [
            '$view' => [
                'title' => $args['title'],
                'name' => 'system/user/message.php'
            ],
            'message' => $args['message'],
            'success' => $args['success'],
            'link'    => $args['link'],
            'label'   => $args['label'],
        ];
    }
}
