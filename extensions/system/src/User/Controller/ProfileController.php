<?php

namespace Pagekit\User\Controller;

use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\User\Entity\User;
use Pagekit\User\Entity\UserRepository;

/**
 * @Route("/profile")
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
        $this->user  = $this('user');
        $this->users = $this('users')->getUserRepository();
    }

    /**
     * @View("system/user/profile.razr.php")
     */
    public function indexAction()
    {
        if (!$this->user->isAuthenticated()) {
            return $this->redirect('@system/auth/login', array('redirect' => $this('url')->current()));
        }

        return array('head.title' => __('Your Profile'), 'user' => $this->user);
    }

    /**
     * @Request({"user": "array"})
     * @Token
     */
    public function saveAction($data)
    {
        if (!$this->user->isAuthenticated()) {
            $this->getApplication()->abort(404);
        }

        try {

            $user = $this->users->find($this->user->getId());

            $name  = trim(@$data['name']);
            $email = trim(@$data['email']);

            if (strlen($name) < 3) {
                throw new Exception(__('Name is invalid.'));
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception(__('Email is invalid.'));
            }

            if ($this->users->where(array('email = ?', 'id <> ?'), array($email, $user->getId()))->first()) {
                throw new Exception(__('Email not available.'));
            }

            $data['name']  = $name;
            $data['email'] = $email;

            $this->users->save($user, $data);

            $this('message')->success(__('Profile updated.'));

        } catch (Exception $e) {
            $this('message')->error($e->getMessage());
        }

        return $this->redirect('@system/profile/index');
    }
}
