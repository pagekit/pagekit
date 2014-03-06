<?php

namespace Pagekit\User\Controller;

use Pagekit\Component\Auth\Auth;
use Pagekit\Component\Auth\Exception\AuthException;
use Pagekit\Component\Auth\Exception\BadCredentialsException;
use Pagekit\Component\Auth\RememberMe;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
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
        $this->user  = $this('user');
        $this->users = $this('users')->getUserRepository();
    }

    /**
     * @Route(methods="POST", defaults= {"_maintenance" = true})
     * @Request({"redirect"})
     * @View("system/user/login.razr.php")
     */
    public function loginAction($redirect = '/')
    {
        if ($this->user->isAuthenticated()) {
            return $this->redirect($redirect);
        }

        return array('meta.title' => __('Login'), 'last_username' => $this('session')->get(Auth::LAST_USERNAME), 'redirect' => ($redirect), 'remember_me_param' => RememberMe::REMEMBER_ME_PARAM);
    }

    /**
     * @Route(defaults= {"_maintenance" = true})
     */
    public function logoutAction()
    {
        return $this('auth')->logout();
    }

    /**
     * @Route(methods="POST", defaults= {"_maintenance" = true})
     * @Request({"credentials": "array", "redirect"})
     */
    public function authenticateAction($credentials, $redirect)
    {
        try {

            if (!$this('csrf')->validate($this('request')->request->get('_csrf'))) {
                throw new AuthException(__('Invalid token. Please try again.'));
            }

            $this('auth')->authorize($user = $this('auth')->authenticate($credentials, false));

            return $this('auth')->login($user);

        } catch (BadCredentialsException $e) {
            $this('message')->error(__('Invalid username or password.'));
        } catch (AuthException $e) {
            $this('message')->error($e->getMessage());
        }

        return $this->redirect($redirect);
    }

    /**
     * @Request({"login"})
     * @View("system/user/reset/request.razr.php")
     */
    public function resetAction($login = "")
    {
        if (!$this('config')->get('mail.enabled')) {
            $this('message')->error(__('Mail system disabled.'));
            return $this->redirect($this('url')->root(true));
        }

        if ($this->user->isAuthenticated()) {
            $this('message')->error(__('You are already logged in.'));
            return $this->redirect($this('url')->root(true));
        }

        $session_key = 'user.authentication.reset_login';

        if ('POST' === $this('request')->getMethod()) {

            try {

                if (empty($login)) {
                    throw new Exception(__('Enter a username or email address.'));
                }

                $this('session')->set($session_key, $login);

                if (!$user = $this->users->findByLogin($login)) {
                    throw new Exception(__('Invalid username or email.'));
                }

                $user->setActivation($this('auth.encoder.native')->hash(md5(uniqid(mt_rand(), true))));

                $this->users->save($user);

                $url = array(
                    'confirm' => $this('url')->to('@system/auth/resetconfirm', array('user' => $user->getUsername(), 'key' => $user->getActivation()), true),
                    'root'    => $this('url')->root(true)
                );

                try {

                    $this('mailer')->create()
                        ->to($user->getEmail())
                        ->from($this('config')->get('mail.from.address'), $this('config')->get('mail.from.name'))
                        ->subject(sprintf('[%s] %s', $this('config')->get('app.site_title'), __('Password Reset')))
                        ->body($this('view')->render('system/user/mails/reset.razr.php', array('username' => $user->getUsername(), 'url' => $url)), 'text/html')
                        ->queue();

                } catch (\Exception $e) {
                    throw new Exception(__('Unable to send confirmation link.'));
                }

                $this('session')->remove($session_key);

                $this('message')->success(__('Check your email for the confirmation link.'));
                return $this->redirect($this('url')->root(true));

            } catch (Exception $e) {
                $this('message')->error($e->getMessage());
            }
        }

        return array('meta.title' => __('Reset'), 'last_login' => $this('session')->get($session_key));
    }

    /**
     * @Route("/reset/confirm")
     * @Request({"user", "key"})
     * @View("system/user/reset/confirm.razr.php")
     */
    public function resetConfirmAction($username = "", $activation = "")
    {
        if (empty($username) or empty($activation) or !$user = $this->users->where(compact('username', 'activation'))->first()) {
            $this('message')->error(__('Invalid key.'));
            return $this->redirect($this('url')->root(true));
        }

        if ('POST' === $this('request')->getMethod()) {

            try {

                $pass1 = $this('request')->request->get('password1');
                $pass2 = $this('request')->request->get('password2');

                if (empty($pass1) || empty($pass2)) {
                    throw new Exception(__('Enter password.'));
                }

                if ($pass1 != $pass2) {
                    throw new Exception(__('The passwords do not match.'));
                }

                $user->setPassword($this('auth.encoder.native')->hash($pass1));
                $this->users->save($user);

                $this('message')->success(__('Your password has been reset.'));
                return $this->redirect($this('url')->root(true));

            } catch (Exception $e) {
                $this('message')->error($e->getMessage());
            }
        }

        return array('meta.title' => __('Reset Confirm'), 'username' => $username, 'activation' => $activation);
    }
}
