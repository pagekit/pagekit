<?php

namespace Pagekit\User\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Application\Exception;
use Pagekit\User\Entity\Role;
use Pagekit\User\Entity\User;

/**
 * @Route("/user/registration")
 */
class RegistrationController extends Controller
{
    /**
     * @Response("app/modules/user/views/registration.php")
     */
    public function indexAction()
    {
        if (App::user()->isAuthenticated()) {
            App::message()->info(__('You are already logged in.'));
            return $this->redirect('/');
        }

        if (App::option('system:user.registration', 'admin') == 'admin') {
            App::message()->info(__('Public user registration is disabled.'));
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

            if (App::user()->isAuthenticated() || App::option('system:user.registration', 'admin') == 'admin') {
                return $this->redirect('/');
            }

            if (!App::csrf()->validate(App::request()->request->get('_csrf'))) {
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

            if (User::query()->orWhere(['username = :username', 'email = :username'], ['username' => $username])->first()) {
                $errors[] = ['field'=> 'username', 'message' => __('Username not available.'), 'dynamic' => true];
            }

            if (User::query()->orWhere(['username = :email', 'email = :email'], ['email' => $email])->first()) {
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
            $user->setPassword(App::get('auth.password')->hash($password));
            $user->setStatus(User::STATUS_BLOCKED);
            $user->setRoles(Role::where(['id' => Role::ROLE_AUTHENTICATED])->get());

            $token = App::get('auth.random')->generateString(32);
            $admin = App::option('system:user.registration') == 'approval';

            if ($verify = App::option('system:user.require_verification')) {

                $user->setActivation($token);

            } elseif ($admin) {

                $user->setActivation($token);
                $user->set('verified', true);

            } else {

                $user->setStatus(User::STATUS_ACTIVE);

            }

            $user->save();

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

            if (!App::request()->isXmlHttpRequest()) {

                App::message()->success($response['success']);
                return $this->redirect('@system/auth/login');
            }

        } catch (Exception $e) {

            if (!App::request()->isXmlHttpRequest()) {

                foreach ($errors as $error) {
                    App::message()->error($error['message']);
                }

            } else {
                $response['errors'] = $errors;
            }
        }

        return App::request()->isXmlHttpRequest() ? $response : $this->redirect(count($errors) ? '@system/registration' : '@system/auth/login');
    }

    /**
     * @Request({"user", "key"})
     */
    public function activateAction($username, $activation)
    {
        if (empty($username) || empty($activation) || !$user = User::where(['username' => $username, 'activation' => $activation, 'status' => User::STATUS_BLOCKED, 'access IS NULL'])->first()) {
            App::message()->error(__('Invalid key.'));
            return $this->redirect('/');
        }

        if ($admin = App::option('system:user.registration') == 'approval' and !$user->get('verified')) {

            $user->setActivation(App::get('auth.random')->generateString(32));
            $this->sendApproveMail($user);

            App::message()->success(__('Your email has been verified. Once an administrator approves your account, you will be notified by email.'));

        } else {

            $user->set('verified', true);
            $user->setStatus(User::STATUS_ACTIVE);
            $user->setActivation('');
            $this->sendWelcomeEmail($user);

            if ($admin) {
                App::message()->success(__('The user\'s account has been activated and the user has been notified about it.'));
            } else {
                App::message()->success(__('Your account has been activated.'));
            }
        }

        $user->save();

        return $this->redirect('@system/auth/login');
    }

    protected function sendWelcomeEmail($user)
    {
        try {

            $mail = App::mailer()->create();
            $mail->setTo($user->getEmail())
                 ->setSubject(__('Welcome to %site%!', ['%site%' => App::system()->config('site.title')]))
                 ->setBody(App::view('app/modules/user/views/mails/welcome.php', compact('user', 'mail')), 'text/html')
                 ->send();

        } catch (\Exception $e) {}
    }

    protected function sendVerificationMail($user)
    {
        try {

            $mail = App::mailer()->create();
            $mail->setTo($user->getEmail())
                 ->setSubject(__('Activate your %site% account.', ['%site%' => App::system()->config('site.title')]))
                 ->setBody(App::view('app/modules/user/views/mails/verification.php', compact('user', 'mail')), 'text/html')
                 ->send();

        } catch (\Exception $e) {
            throw new Exception(__('Unable to send verification link.'));
        }
    }

    protected function sendApproveMail($user)
    {
        try {

            $mail = App::mailer()->create();
            $mail->setTo(App::option('system:mail.from.address'))
                 ->setSubject(__('Approve an account at %site%.', ['%site%' => App::system()->config('site.title')]))
                 ->setBody(App::view('app/modules/user/views/mails/approve.php', compact('user', 'mail')), 'text/html')
                 ->send();

        } catch (\Exception $e) {}
    }
}
