<?php

namespace Pagekit\User\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Exception;
use Pagekit\User\Model\User;

class ProfileController
{
    public function indexAction()
    {
        $user = App::user();

        if (!$user->isAuthenticated()) {
            return App::redirect('@user/login', ['redirect' => App::url()->current()]);
        }

        return [
            '$view' => [
                'title' => __('Your Profile'),
                'name'  => 'system/user/profile.php'
            ],
            '$data' => [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ]
        ];
    }

    /**
     * @Request({"user": "array"}, csrf=true)
     */
    public function saveAction($data)
    {
        $user = App::user();

        if (!$user->isAuthenticated()) {
            App::abort(404);
        }

        try {

            $user = User::find($user->id);

            if ($password = @$data['password_new']) {

                if (!App::auth()->getUserProvider()->validateCredentials($user, ['password' => @$data['password_old']])) {
                    throw new Exception(__('Invalid Password.'));
                }

                if (trim($password) != $password || strlen($password) < 3) {
                    throw new Exception(__('Invalid Password.'));
                }

                $user->password = App::get('auth.password')->hash($password);
            }

            if (@$data['email'] != $user->email) {
                $user->set('verified', false);
            }

            $user->name = @$data['name'];
            $user->email = @$data['email'];

            $user->validate();
            $user->save();

            return ['message' => 'success'];

        } catch (Exception $e) {
            App::abort(400, $e->getMessage());
        }
    }
}
