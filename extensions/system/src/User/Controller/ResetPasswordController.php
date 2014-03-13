<?php

namespace Pagekit\User\Controller;

use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\User\Entity\UserRepository;
use Pagekit\User\Model\UserInterface;

/**
 * @Route("/password")
 */
class ResetPasswordController extends Controller
{
    const RESET_LOGIN = 'user.authentication.reset_login';

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
     * @Route("/request")
     * @View("system/user/reset/request.razr.php")
     */
    public function requestAction()
    {
        if (!$this('config')->get('mail.enabled')) {
            $this('message')->error(__('Mail system disabled.'));
            return $this->redirect($this('url')->root(true));
        }

        if ($this->user->isAuthenticated()) {
            $this('message')->error(__('You are already logged in.'));
            return $this->redirect($this('url')->root(true));
        }

        return array('head.title' => __('Reset'), 'last_login' => $this('session')->get(self::RESET_LOGIN));
    }

    /**
     * @Route("/reset")
     * @Request({"login"})
     * @View("system/user/reset/request.razr.php")
     */
    public function resetAction($login)
    {
        try {

            if (!$this('config')->get('mail.enabled')) {
                throw new Exception(__('Mail system disabled.'));
            }

            if ($this->user->isAuthenticated()) {
                throw new Exception(__('You are already logged in.'));
            }

            if (!$this('csrf')->validate($this('request')->request->get('_csrf'))) {
                throw new Exception(__('Invalid token. Please try again.'));
            }

            if (empty($login)) {
                throw new Exception(__('Enter a username or email address.'));
            }

            $this('session')->set(self::RESET_LOGIN, $login);

            if (!$user = $this->users->findByLogin($login)) {
                throw new Exception(__('Invalid username or email.'));
            }

            $user->setActivation($this('auth.encoder.native')->hash(md5(uniqid(mt_rand(), true))));

            $this->users->save($user);

            $url = array(
                'confirm' => $this('url')->to('@system/resetpassword/confirm', array('user' => $user->getUsername(), 'key' => $user->getActivation()), true),
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

            $this('session')->remove(self::RESET_LOGIN);

            $this('message')->success(__('Check your email for the confirmation link.'));

            return $this->redirect($this('url')->root(true));

        } catch (Exception $e) {
            $this('message')->error($e->getMessage());
        }

        return $this->redirect($this('url')->previous(true));
    }

    /**
     * @Route("/confirm")
     * @Request({"user", "key"})
     * @View("system/user/reset/confirm.razr.php")
     */
    public function confirmAction($username = "", $activation = "")
    {
        if (empty($username) or empty($activation) or !$user = $this->users->where(compact('username', 'activation'))->first()) {
            $this('message')->error(__('Invalid key.'));
            return $this->redirect($this('url')->root(true));
        }

        if ('POST' === $this('request')->getMethod()) {

            try {

                if (!$this('csrf')->validate($this('request')->request->get('_csrf'))) {
                    throw new Exception(__('Invalid token. Please try again.'));
                }

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

        return array('head.title' => __('Reset Confirm'), 'username' => $username, 'activation' => $activation);
    }
}
