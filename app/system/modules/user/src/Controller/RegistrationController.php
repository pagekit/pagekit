<?php

namespace Pagekit\User\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Exception;
use Pagekit\Module\Module;
use Pagekit\User\Model\User;

class RegistrationController
{
    /**
     * @var Module
     */
    protected $module;

    public function __construct()
    {
        $this->module = App::module('system/user');
    }

    public function indexAction()
    {
        if (App::user()->isAuthenticated()) {
            return App::redirect();
        }

        if ($this->module->config('registration') == 'admin') {
            return App::redirect();
        }

        return [
            '$view' => [
                'title' => __('User Registration'),
                'name' => 'system/user/registration.php'
            ]
        ];
    }

    /**
     * @Request({"user": "array"})
     */
    public function registerAction($data)
    {
        try {

            if (App::user()->isAuthenticated() || $this->module->config('registration') == 'admin') {
                return App::redirect();
            }

            if (!App::csrf()->validate()) {
                throw new Exception(__('Invalid token. Please try again.'));
            }

            $password = @$data['password'];
            if (trim($password) != $password || strlen($password) < 6) {
                throw new Exception(__('Password must be 6 characters or longer.'));
            }

            $user = User::create([
                'registered' => new \DateTime,
                'name' => @$data['name'],
                'username' => @$data['username'],
                'email' => @$data['email'],
                'password' => App::get('auth.password')->hash($password),
                'status' => User::STATUS_BLOCKED
            ]);

            $token = App::get('auth.random')->generateString(32);
            $admin = $this->module->config('registration') == 'approval';

            if ($verify = $this->module->config('require_verification') or $admin) {
                $user->activation = $token;
            } else {
                $user->status = User::STATUS_ACTIVE;
            }

            $user->validate();
            $user->save();

            if ($verify) {
                $this->sendVerificationMail($user);
                $message = __('Complete your registration by clicking the link provided in the mail that has been sent to you.');
            } elseif ($admin) {
                $this->sendApproveMail($user);
                $message = __('Your user account has been created and is pending approval by the site administrator.');
            } else {
                $this->sendWelcomeEmail($user);
                $message = __('Your user account has been created.');
            }

        } catch (Exception $e) {
            App::abort(400, $e->getMessage());
        }

        App::message()->success($message);

        return [
            'redirect' => App::url('@user/login')
        ];
    }

    /**
     * @Request({"user", "key"})
     */
    public function activateAction($username, $activation)
    {
        if (empty($username) || empty($activation) || !$user = User::where(['username' => $username, 'activation' => $activation, 'login IS NULL'])->first()) {
            App::abort(400, __('Invalid key.'));
        }

        $verifying = false;
        if ($this->module->config('require_verification') && !$user->get('verified')) {
            $user->set('verified', true);
            $verifying = true;
        }

        if ($this->module->config('registration') === 'approval' && $user->status === User::STATUS_BLOCKED && $verifying) {
            $user->activation = App::get('auth.random')->generateString(32);
            $this->sendApproveMail($user);
            $message = __('Your email has been verified. Once an administrator approves your account, you will be notified by email.');
        } else {
            $user->status = User::STATUS_ACTIVE;
            $user->activation = '';
            $this->sendWelcomeEmail($user);
            $message = $verifying ?  __('Your account has been activated.') : __('The user\'s account has been activated and the user has been notified about it.');
        }

        $user->save();

        App::message()->success($message);

        return App::redirect('@user/login');
    }

    protected function sendWelcomeEmail($user)
    {
        try {

            $mail = App::mailer()->create();
            $mail->setTo($user->email)
                ->setSubject(__('Welcome to %site%!', ['%site%' => App::module('system/site')->config('title')]))
                ->setBody(App::view('system/user:mails/welcome.php', compact('user', 'mail')), 'text/html')
                ->send();

        } catch (\Exception $e) {
        }
    }

    protected function sendVerificationMail($user)
    {
        try {

            $mail = App::mailer()->create();
            $mail->setTo($user->email)
                ->setSubject(__('Activate your %site% account.', ['%site%' => App::module('system/site')->config('title')]))
                ->setBody(App::view('system/user:mails/verification.php', compact('user', 'mail')), 'text/html')
                ->send();

        } catch (\Exception $e) {
            throw new Exception(__('Unable to send verification link.'));
        }
    }

    protected function sendApproveMail($user)
    {
        try {

            $mail = App::mailer()->create();
            $mail->setTo(App::module('system/mail')->config('from_address'))
                ->setSubject(__('Approve an account at %site%.', ['%site%' => App::module('system/site')->config('title')]))
                ->setBody(App::view('system/user:mails/approve.php', compact('user', 'mail')), 'text/html')
                ->send();

        } catch (\Exception $e) {
        }
    }
}
