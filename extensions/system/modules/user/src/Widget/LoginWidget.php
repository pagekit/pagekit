<?php

namespace Pagekit\User\Widget;

use Pagekit\Application as App;
use Pagekit\Auth\Auth;
use Pagekit\Auth\RememberMe;
use Pagekit\Widget\Model\TypeInterface;
use Pagekit\Widget\Model\WidgetInterface;

class LoginWidget implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'widget.user.login';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Login');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(WidgetInterface $widget = null)
    {
        return __('Displays a user login form.');
    }

    /**
     * {@inheritdoc}
     */
    public function render(WidgetInterface $widget, $options = [])
    {
        $user = App::user();

        if ($user->isAuthenticated()) {
            $redirect = $widget->get('redirect.logout') ?: App::url()->current(true);
            return App::view('extensions/system/modules/user/views/login/logout.razr', compact('widget', 'user', 'options', 'redirect'));
        }

        $redirect          = $widget->get('redirect.login') ?: App::url()->current(true);
        $last_username     = App::session()->get(Auth::LAST_USERNAME);
        $remember_me_param = RememberMe::REMEMBER_ME_PARAM;
        return App::view('extensions/system/modules/user/views/login/login.razr', compact('widget', 'options', 'user', 'last_username', 'remember_me_param', 'redirect'));
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return App::view('extensions/system/modules/user/views/login/edit.razr', compact('widget'));
    }
}
