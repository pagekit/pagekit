<?php

namespace Pagekit\User\Widget;

use Pagekit\Application as App;
use Pagekit\Auth\Auth;
use Pagekit\Auth\RememberMe;
use Pagekit\Widget\Model\Type;
use Pagekit\Widget\Model\WidgetInterface;

class LoginWidget extends Type
{
    public function __construct()
    {
        parent::__construct('site.user.login', __('Login'), __('Displays a user login form.'));
    }

    /**
     * {@inheritdoc}
     */
    public function render(WidgetInterface $widget, $options = [])
    {
        $user = App::user();

        if ($user->isAuthenticated()) {
            $redirect = $widget->get('redirect.logout') ?: App::url()->current(true);
            return App::view('system/user:views/login/logout.php', compact('widget', 'user', 'options', 'redirect'));
        }

        $redirect          = $widget->get('redirect.login') ?: App::url()->current(true);
        $last_username     = App::session()->get(Auth::LAST_USERNAME);
        $remember_me_param = RememberMe::REMEMBER_ME_PARAM;

        return App::view('system/user:views/login/login.php', compact('widget', 'options', 'user', 'last_username', 'remember_me_param', 'redirect'));
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return App::view('system/user:views/login/edit.php', compact('widget'));
    }
}
