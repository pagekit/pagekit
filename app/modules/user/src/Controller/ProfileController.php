<?php

namespace Pagekit\User\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Application\Exception;
use Pagekit\User\Entity\User;
use Pagekit\User\Event\ProfileSaveEvent;

/**
 * @Route("/user/profile")
 */
class ProfileController extends Controller
{
    /**
     * @Response("app/modules/user/views/profile.php")
     */
    public function indexAction()
    {
        if (!App::user()->isAuthenticated()) {
            return $this->redirect('@system/auth/login', ['redirect' => App::url()->current()]);
        }

        return ['head.title' => __('Your Profile'), 'user' => App::user()];
    }

    /**
     * @Request({"user": "array"}, csrf=true)
     */
    public function saveAction($data)
    {
        if (!App::user()->isAuthenticated()) {
            App::abort(404);
        }

        try {

            $user = User::find(App::user()->getId());

            $name    = trim(@$data['name']);
            $email   = trim(@$data['email']);
            $passNew = @$data['password_new'];
            $passOld = @$data['password_old'];

            if (strlen($name) < 3) {
                throw new Exception(__('Name is invalid.'));
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception(__('Email is invalid.'));
            }

            if (User::where(['email = ?', 'id <> ?'], [$email, $user->getId()])->first()) {
                throw new Exception(__('Email not available.'));
            }

            if ($passNew) {

                if (!App::auth()->getUserProvider()->validateCredentials($this->user, ['password' => $passOld])) {
                    throw new Exception(__('Invalid Password.'));
                }

                if (trim($passNew) != $passNew || strlen($passNew) < 3) {
                    throw new Exception(__('New Password is invalid.'));
                }

                $user->setPassword(App::get('auth.password')->hash($passNew));
            }

            if ($email != $user->getEmail()) {
                $user->set('verified', false);
            }

            $user->setName($name);
            $user->setEmail($email);

            App::trigger('system.user.profile.save', new ProfileSaveEvent($user, $data));

            $user->save();

            App::trigger('system.user.profile.saved', new ProfileSaveEvent($user, $data));

            App::message()->success(__('Profile updated.'));

        } catch (Exception $e) {
            App::message()->error($e->getMessage());
        }

        return $this->redirect('@system/profile');
    }
}
