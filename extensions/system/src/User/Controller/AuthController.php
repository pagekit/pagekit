<?php

namespace Pagekit\User\Controller;

use Pagekit\Component\Auth\Auth;
use Pagekit\Component\Auth\Exception\AuthException;
use Pagekit\Component\Auth\Exception\BadCredentialsException;
use Pagekit\Component\Auth\RememberMe;
use Pagekit\Framework\Controller\Controller;
use Pagekit\User\Entity\UserRepository;
use Pagekit\User\Model\UserInterface;

/**
 * @Route("/user")
 */
class AuthController extends Controller
{
    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var UserRepository
     */
    protected $users;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->user  = $this['user'];
        $this->users = $this['users']->getUserRepository();
    }

    /**
     * @Route(methods="POST", defaults={"_maintenance"=true})
     * @Request({"redirect"})
     * @Response("extension://system/views/user/login.razr")
     */
    public function loginAction($redirect = '')
    {
        if ($this->user->isAuthenticated()) {
            $this['message']->info(__('You are already logged in.'));
            return $this->redirect('/');
        }

        return ['head.title' => __('Login'), 'last_username' => $this['session']->get(Auth::LAST_USERNAME), 'redirect' => $redirect, 'remember_me_param' => RememberMe::REMEMBER_ME_PARAM];
    }

    /**
     * @Route(defaults={"_maintenance" = true})
     */
    public function logoutAction()
    {
        return $this['auth']->logout();
    }

    /**
     * @Route(methods="POST", defaults={"_maintenance" = true})
     * @Request({"credentials": "array"})
     */
    public function authenticateAction($credentials)
    {
        try {

            if (!$this['csrf']->validate($this['request']->request->get('_csrf'))) {
                throw new AuthException(__('Invalid token. Please try again.'));
            }

            $this['auth']->authorize($user = $this['auth']->authenticate($credentials, false));

            return $this['auth']->login($user);

        } catch (BadCredentialsException $e) {
            $this['message']->error(__('Invalid username or password.'));
        } catch (AuthException $e) {
            $this['message']->error($e->getMessage());
        }

        return $this->redirect($this['url']->previous());
    }
}
