<?php

namespace Pagekit\User\Controller;

use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\User\Entity\User;
use Pagekit\User\Entity\UserRepository;
use Pagekit\User\Event\ProfileSaveEvent;

/**
 * @Route("/user/profile")
 */
class ProfileController extends Controller
{
    /**
     * @var User
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
     * @Response("extension://system/views/user/profile.razr")
     */
    public function indexAction()
    {
        if (!$this->user->isAuthenticated()) {
            return $this->redirect('@system/auth/login', ['redirect' => $this['url']->current()]);
        }

        return ['head.title' => __('Your Profile'), 'user' => $this->user];
    }

    /**
     * @Request({"user": "array"}, csrf=true)
     */
    public function saveAction($data)
    {
        if (!$this->user->isAuthenticated()) {
            $this->getApplication()->abort(404);
        }

        try {

            $user = $this->users->find($this->user->getId());

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

            if ($this->users->where(['email = ?', 'id <> ?'], [$email, $user->getId()])->first()) {
                throw new Exception(__('Email not available.'));
            }

            if ($passNew) {

                if (!$this['auth']->getUserProvider()->validateCredentials($this->user, ['password' => $passOld])) {
                    throw new Exception(__('Invalid Password.'));
                }

                if (trim($passNew) != $passNew || strlen($passNew) < 3) {
                    throw new Exception(__('New Password is invalid.'));
                }

                $user->setPassword($this['auth.password']->hash($passNew));
            }

            if ($email != $user->getEmail()) {
                $user->set('verified', false);
            }

            $user->setName($name);
            $user->setEmail($email);

            $this['events']->dispatch('system.user.profile.save', new ProfileSaveEvent($user, $data));

            $this->users->save($user);

            $this['events']->dispatch('system.user.profile.saved', new ProfileSaveEvent($user, $data));

            $this['message']->success(__('Profile updated.'));

        } catch (Exception $e) {
            $this['message']->error($e->getMessage());
        }

        return $this->redirect('@system/profile');
    }
}
