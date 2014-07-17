<?php

namespace Pagekit\User\Widget;

use Pagekit\Component\Auth\Auth;
use Pagekit\Component\Auth\RememberMe;
use Pagekit\Widget\Model\Type;
use Pagekit\Widget\Model\WidgetInterface;

class LoginWidget extends Type
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
        $user = $this['user'];

        if ($user->isAuthenticated()) {
            $redirect = $widget->get('redirect.logout') ?: $this['url']->current(true);
            return $this['view']->render('extension://system/views/widgets/login/logout.razr', compact('widget', 'user', 'options', 'redirect'));
        }

        $redirect          = $widget->get('redirect.login') ?: $this['url']->current(true);
        $last_username     = $this['session']->get(Auth::LAST_USERNAME);
        $remember_me_param = RememberMe::REMEMBER_ME_PARAM;
        return $this['view']->render('extension://system/views/widgets/login/login.razr', compact('widget', 'options', 'user', 'last_username', 'remember_me_param', 'redirect'));
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return $this['view']->render('extension://system/views/widgets/login/edit.razr', compact('widget'));
    }
}
