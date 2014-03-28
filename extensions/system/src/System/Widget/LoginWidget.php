<?php

namespace Pagekit\System\Widget;

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
    public function getDescription()
    {
        return __('Displays a user login form.');
    }

    /**
     * {@inheritdoc}
     */
    public function getInfo(WidgetInterface $widget)
    {
        return __('Displays a user login form.');
    }

    /**
     * {@inheritdoc}
     */
    public function render(WidgetInterface $widget, $options = array())
    {
        $user = $this('user');

        if ($user->isAuthenticated()) {
            return $this('view')->render('system/widgets/login/logout.razr.php', compact('widget', 'user', 'options'));
        }

        return $this('view')->render('system/widgets/login/login.razr.php', array('widget' => $widget, 'options' => $options, 'user' => $user, 'last_username' => $this('session')->get(Auth::LAST_USERNAME), 'remember_me_param' => RememberMe::REMEMBER_ME_PARAM));
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return $this('view')->render('system/widgets/login/edit.razr.php', compact('widget'));
    }
}
