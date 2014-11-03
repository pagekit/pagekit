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
        $this->users = $this['users']->getUserRepository();
        $this->roles = $this['users']->getRoleRepository();
    }

    /**
     * @Response("extension://system/views/user/registration.razr")
     */
    public function indexAction()
    {
        if ($this['user']->isAuthenticated()) {
            $this['message']->info(__('You are already logged in.'));
            return $this->redirect('/');
        }

        if ($this['option']->get('system:user.registration', 'admin') == 'admin') {
            $this['message']->info(__('Public user registration is disabled.'));
            return $this->redirect('/');
        }

        return ['head.title' => __('User Registration')];
    }

    /**
     * @Request({"user": "array"})
     * @Response("json")
     */
    public function registerAction($data)
    {

        $response = ['success' => false];
        $errors   = [];

        try {

            if ($this['user']->isAuthenticated() || $this['option']->get('system:user.registration', 'admin') == 'admin') {
                return $this->redirect('/');
            }

            if (!$this['csrf']->validate($this['request']->request->get('_csrf'))) {
                throw new Exception(__('Invalid token. Please try again.'));
            }

            $name     = trim(@$data['name']);
            $username = trim(@$data['username']);
            $email    = trim(@$data['email']);
            $password = @$data['password'];

            if (empty($name)) {
                $errors[] = ['field'=> 'name', 'message' => __('Name required.')];
            }

            if (empty($password)) {
                $errors[] = ['field'=> 'password', 'message' => __('Password required.')];
            }

            if (strlen($username) < 3 || !preg_match('/^[a-zA-Z0-9_\-]+$/', $username)) {
                $errors[] = ['field'=> 'username', 'message' => __('Username is invalid.')];
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = ['field'=> 'email', 'message' => __('Email is invalid.')];
            }

            if ($this->users->query()->orWhere(['username = :username', 'email = :username'], ['username' => $username])->first()) {
                $errors[] = ['field'=> 'username', 'message' => __('Username not available.'), 'dynamic' => true];
            }

            if ($this->users->query()->orWhere(['username = :email', 'email = :email'], ['email' => $email])->first()) {
                $errors[] = ['field'=> 'email', 'message' => __('Email not available.'), 'dynamic' => true];
            }

            if (count($errors)) {
                throw new Exception(__('Signup failed'));
            }

            $user = new User;
            $user->setRegistered(new \DateTime);
            $user->setName($name);
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPassword($this['auth.password']->hash($password));
            $user->setStatus(UserInterface::STATUS_BLOCKED);
            $user->setRoles($this->roles->where(['id' => RoleInterface::ROLE_AUTHENTICATED])->get());

            $token = $this['auth.random']->generateString(32);
            $admin = $this['option']->get('system:user.registration') == 'approval';

            if ($verify = $this['option']->get('system:user.require_verification')) {

                $user->setActivation($token);

            } elseif ($admin) {

                $user->setActivation($token);
                $user->set('verified', true);

            } else {

                $user->setStatus(UserInterface::STATUS_ACTIVE);

            }

            $this->users->save($user);

            if ($verify) {

                $this->sendVerificationMail($user);
                $response['success'] = __('Your user account has been created. Complete your registration, by clicking the link provided in the mail that has been sent to you.');

            } elseif ($admin) {

                $this->sendApproveMail($user);
                $response['success'] = __('Your user account has been created and is pending approval by the site administrator.');

            } else {

                $this->sendWelcomeEmail($user);
                $response['success'] = __('Your user account has been created.');

            }

            if (!$response['success']) {
                $response['success'] = true;
            }

            if (!$this['request']->isXmlHttpRequest()) {

                $this['message']->success($response['success']);
                return $this->redirect('@system/auth/login');
            }

        } catch (Exception $e) {

            if (!$this['request']->isXmlHttpRequest()) {

                foreach ($errors as $error) {
                    $this['message']->error($error['message']);
                }

            } else {
                $response['errors'] = $errors;
            }
        }

        return $this['request']->isXmlHttpRequest() ? $response : $this->redirect(count($errors) ? '@system/registration' : '@system/auth/login');
    }

    /**
     * @Request({"user", "key"})
     */
    public function activateAction($username, $activation)
    {
        if (empty($username) || empty($activation) || !$user = $this->users->where(['username' => $username, 'activation' => $activation, 'status' => UserInterface::STATUS_BLOCKED, 'access IS NULL'])->first()) {
            $this['message']->error(__('Invalid key.'));
            return $this->redirect('/');
        }

        if ($admin = $this['option']->get('system:user.registration') == 'approval' and !$user->get('verified')) {

            $user->setActivation($this['auth.random']->generateString(32));
            $this->sendApproveMail($user);

            $this['message']->success(__('Your email has been verified. Once an administrator approves your account, you will be notified by email.'));

        } else {

            $user->set('verified', true);
            $user->setStatus(UserInterface::STATUS_ACTIVE);
            $user->setActivation('');
            $this->sendWelcomeEmail($user);

            if ($admin) {
                $this['message']->success(__('The user\'s account has been activated and the user has been notified about it.'));
            } else {
                $this['message']->success(__('Your account has been activated.'));
            }
        }

        $this->users->save($user);

        return $this->redirect('@system/auth/login');
    }

    protected function sendWelcomeEmail($user)
    {
        try {

            $mail = $this['mailer']->create();
            $mail->setTo($user->getEmail())
                 ->setSubject(__('Welcome to %site%!', ['%site%' => $this['option']->get('system:app.site_title')]))
                 ->setBody($this['view']->render('extension://system/views/user/mails/welcome.razr', compact('user', 'mail')), 'text/html')
                 ->send();

        } catch (\Exception $e) {}
    }

    protected function sendVerificationMail($user)
    {
        try {

            $mail = $this['mailer']->create();
            $mail->setTo($user->getEmail())
                 ->setSubject(__('Activate your %site% account.', ['%site%' => $this['option']->get('system:app.site_title')]))
                 ->setBody($this['view']->render('extension://system/views/user/mails/verification.razr', compact('user', 'mail')), 'text/html')
                 ->send();

        } catch (\Exception $e) {
            throw new Exception(__('Unable to send verification link.'));
        }
    }

    protected function sendApproveMail($user)
    {
        try {

            $mail = $this['mailer']->create();
            $mail->setTo($this['option']->get('system:mail.from.address'))
                 ->setSubject(__('Approve an account at %site%.', ['%site%' => $this['option']->get('system:app.site_title')]))
                 ->setBody($this['view']->render('extension://system/views/user/mails/approve.razr', compact('user', 'mail')), 'text/html')
                 ->send();

        } catch (\Exception $e) {}
    }
}
