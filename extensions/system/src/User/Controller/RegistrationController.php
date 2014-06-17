<?php

namespace Pagekit\User\Controller;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\User\Entity\User;
use Pagekit\User\Entity\UserRepository;
use Pagekit\User\Model\RoleInterface;
use Pagekit\User\Model\UserInterface;

/**
 * @Route("/user/registration")
 */
class RegistrationController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $users;

    /**
     * @var Repository
     */
    protected $roles;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->users = $this('users')->getUserRepository();
        $this->roles = $this('users')->getRoleRepository();
    }

    /**
     * @View("system/user/registration.razr.php")
     */
    public function indexAction()
    {
        if ($this('user')->isAuthenticated() || $this('option')->get('system:user.registration', 'admin') == 'admin') {
            return $this->redirect('@frontpage');
        }

        return array('head.title' => __('User Registration'));
    }

    /**
     * @Request({"user": "array"})
     */
    public function registerAction($data)
    {
        try {

            if ($this('user')->isAuthenticated() || $this('option')->get('system:user.registration', 'admin') == 'admin') {
                return $this->redirect('@frontpage');
            }

            if (!$this('csrf')->validate($this('request')->request->get('_csrf'))) {
                throw new Exception(__('Invalid token. Please try again.'));
            }

            $name     = trim(@$data['name']);
            $username = trim(@$data['username']);
            $email    = trim(@$data['email']);
            $password = @$data['password'];

            if (empty($name)) {
                throw new Exception(__('Name required.'));
            }

            if (empty($password)) {
                throw new Exception(__('Password required.'));
            }

            if (strlen($username) < 3 || !preg_match('/^[a-zA-Z0-9_\-]+$/', $username)) {
                throw new Exception(__('Username is invalid.'));
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception(__('Email is invalid.'));
            }

            if ($this->users->query()->orWhere(array('username = :username', 'email = :username'), array('username' => $username))->first()) {
                throw new Exception(__('Username not available.'));
            }

            if ($this->users->query()->orWhere(array('username = :email', 'email = :email'), array('email' => $email))->first()) {
                throw new Exception(__('Email not available.'));
            }

            $user = new User;
            $user->setRegistered(new \DateTime);
            $user->setName($name);
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPassword($this('auth.password')->hash($password));
            $user->setStatus(UserInterface::STATUS_BLOCKED);
            $user->setRoles($this->roles->where(array('id' => RoleInterface::ROLE_AUTHENTICATED))->get());

            $token = $this('auth.random')->generateString(128);
            $admin = $this('option')->get('system:user.registration') == 'approval';

            if ($verify = $this('option')->get('system:user.require_verification')) {

                $user->setActivation($token);

            } elseif ($admin) {

                $user->setActivation($token);
                $user->set('verified', true);

            } else {

                $user->setStatus(UserInterface::STATUS_ACTIVE);

            }

            $this->users->save($user);

            if ($verify) {

                $this->sendVerificationMail($user, $admin ? 'verification.admin' : 'verification');
                $this('message')->success(__('Your user account has been created. Complete your registration, by clicking the link provided in the mail that has been sent to you.'));

            } elseif ($admin) {

                $this->sendActivateMail($user, 'activate');
                $this('message')->success(__('Your user account has been created and is pending approval by the site administrator.'));

            } else {

                $this('message')->success(__('Your user account has been created.'));

            }

            return $this->redirect('@system/auth/login');

        } catch (Exception $e) {
            $this('message')->error($e->getMessage());
        }

        return $this->redirect('@system/registration/index');
    }

    /**
     * @Request({"user", "key"})
     */
    public function activateAction($username, $activation)
    {
        if (empty($username) or empty($activation) or !$user = $this->users->where(array('username' => $username, 'activation' => $activation, 'status' => UserInterface::STATUS_BLOCKED, 'access IS NULL'))->first()) {
            $this('message')->error(__('Invalid key.'));
            return $this->redirect('@frontpage');
        }

        if ($admin = $this('option')->get('system:user.registration') == 'approval' and !$user->get('verified')) {

            $user->setActivation($this('auth.random')->generateString(128));
            $this->sendActivateMail($user, 'verification.activate');

            $this('message')->success(__('Your email has been verified. Once an administrator approves your account, you will be notified by email, and you can login to the site.'));

        } else {

            if ($admin) {

                $this->sendActivatedMail($user);
                $this('message')->success(__('The user\'s account has been activated and the user has been notified about it.'));

            } else {

                $this('message')->success(__('Your account has been activated.'));

            }

            $user->set('verified', true);
            $user->setStatus(UserInterface::STATUS_ACTIVE);
            $user->setActivation('');

        }

        $this->users->save($user);

        return $this->redirect('@system/auth/login');
    }

    protected function sendActivateMail($user, $mail)
    {
        try {

            $this('mailer')->create()
                ->setTo($this('option')->get('system:mail.from.address'))
                ->setSubject(__('Please approve registration at %site%!', array('%site%' => $this('option')->get('system:app.site_title'))))
                ->setBody($this('view')->render(sprintf('system/user/mails/%s.razr.php', $mail), compact('user')), 'text/html')
                ->send();

        } catch (\Exception $e) {}
    }

    protected function sendVerificationMail($user, $mail)
    {
        try {

            $this('mailer')->create()
                ->setTo($user->getEmail())
                ->setSubject(__('Please confirm your registration at %site%', array('%site%' => $this('option')->get('system:app.site_title'))))
                ->setBody($this('view')->render(sprintf('system/user/mails/%s.razr.php', $mail), compact('user')), 'text/html')
                ->send();

        } catch (\Exception $e) {
            throw new Exception(__('Unable to send verification link.'));
        }
    }

    protected function sendActivatedMail($user)
    {
        try {

            $this('mailer')->create()
                ->setTo($user->getEmail())
                ->setSubject(__('Account activated at %site%', array('%site%' => $this('option')->get('system:app.site_title'))))
                ->setBody($this('view')->render('system/user/mails/activated.razr.php', compact('user')), 'text/html')
                ->send();

        } catch (\Exception $e) {}
    }
}
