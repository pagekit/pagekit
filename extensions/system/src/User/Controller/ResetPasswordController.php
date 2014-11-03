<?php

namespace Pagekit\User\Controller;

use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\User\Entity\UserRepository;
use Pagekit\User\Model\UserInterface;

/**
 * @Route("/user/password")
 */
class ResetPasswordController extends Controller
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
     * @Response("extension://system/views/user/reset/request.razr")
     */
    public function indexAction()
    {
        if ($this->user->isAuthenticated()) {
            return $this->redirect('/');
        }

        return ['head.title' => __('Reset')];
    }

    /**
     * @Request({"email"})
     * @Response("extension://system/views/user/reset/request.razr")
     */
    public function resetAction($email)
    {
        try {

            if ($this->user->isAuthenticated()) {
                return $this->redirect('/');
            }

            if (!$this['csrf']->validate($this['request']->request->get('_csrf'))) {
                throw new Exception(__('Invalid token. Please try again.'));
            }

            if (empty($email)) {
                throw new Exception(__('Enter a email address.'));
            }

            if (!$user = $this->users->findByEmail($email)) {
                throw new Exception(__('Invalid email address.'));
            }

            if ($user->isBlocked()) {
                throw new Exception(__('Your account has not been activated or is blocked.'));
            }

            $user->setActivation($this['auth.random']->generateString(32));

            $url = $this['url']->route('@system/resetpassword/confirm', ['user' => $user->getUsername(), 'key' => $user->getActivation()], true);

            try {

                $mail = $this['mailer']->create();
                $mail->setTo($user->getEmail())
                     ->setSubject(__('Reset password for %site%.', ['%site%' => $this['config']->get('app.site_title')]))
                     ->setBody($this['view']->render('extension://system/views/user/mails/reset.razr', compact('user', 'url', 'mail')), 'text/html')
                     ->send();

            } catch (\Exception $e) {
                throw new Exception(__('Unable to send confirmation link.'));
            }

            $this->users->save($user);

            $this['message']->success(__('Check your email for the confirmation link.'));

            return $this->redirect('/');

        } catch (Exception $e) {
            $this['message']->error($e->getMessage());
        }

        return $this->redirect('@system/resetpassword');
    }

    /**
     * @Request({"user", "key"})
     * @Response("extension://system/views/user/reset/confirm.razr")
     */
    public function confirmAction($username = "", $activation = "")
    {
        if (empty($username) || empty($activation) || !$user = $this->users->where(compact('username', 'activation'))->first()) {
            $this['message']->error(__('Invalid key.'));
            return $this->redirect('/');
        }

        if ($user->isBlocked()) {
            $this['message']->error(__('Your account has not been activated or is blocked.'));
            return $this->redirect('/');
        }

        if ('POST' === $this['request']->getMethod()) {

            try {

                if (!$this['csrf']->validate($this['request']->request->get('_csrf'))) {
                    throw new Exception(__('Invalid token. Please try again.'));
                }

                $password = $this['request']->request->get('password');

                if (empty($password)) {
                    throw new Exception(__('Enter password.'));
                }

                if ($password != trim($password)) {
                    throw new Exception(__('Invalid password.'));
                }

                $user->setPassword($this['auth.password']->hash($password));
                $user->setActivation(null);

                $this->users->save($user);

                $this['message']->success(__('Your password has been reset.'));
                return $this->redirect('/');

            } catch (Exception $e) {
                $this['message']->error($e->getMessage());
            }
        }

        return ['head.title' => __('Reset Confirm'), 'username' => $username, 'activation' => $activation];
    }
}
