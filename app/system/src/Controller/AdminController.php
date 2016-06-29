<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;
use Pagekit\Auth\Auth;
use Pagekit\User\Model\User;

class AdminController
{
    /**
     * @Access(admin=true)
     */
    public function indexAction()
    {
        return App::redirect('@dashboard');
    }

    /**
     * @Route("/admin/login", defaults={"_maintenance"=true})
     * @Request({"redirect": "string", "message": "string"})
     */
    public function loginAction($redirect = '', $message = '')
    {
        if (App::user()->isAuthenticated()) {
            return App::redirect('@system');
        }

        return [
            '$view' => [
                'title'  => __('Login'),
                'name'   => 'system/theme:views/login.php',
                'layout' => false
            ],
            'last_username' => App::session()->get(Auth::LAST_USERNAME),
            'redirect' => $redirect ?: App::url('@system'),
            'message' => $message
        ];
    }

    /**
     * @Access(admin=true)
     * @Request({"order": "array"})
     */
    public function adminMenuAction($order)
    {
        if (!$order) {
            App::abort(400, __('Missing order data.'));
        }

        $user = User::find(App::user()->id);
        $user->set('admin.menu', $order);
        $user->save();

        return ['message' => __('Order saved.')];
    }
}
