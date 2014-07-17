<?php

namespace Pagekit\User\Link;

use Pagekit\System\Link\Link;

class User extends Link
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return __('User');
    }

    /**
     * {@inheritdoc}
     */
    public function accept($route)
    {
        return in_array($route, array_keys($this->getRoutes()));
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm($link, $params = [], $context = '')
    {
        $routes = $this->getRoutes();

        return $this['view']->render('extension://system/views/admin/user/link.razr', compact('link', 'params', 'routes'));
    }

    protected function getRoutes()
    {
        return [
            '@system/auth/login'    => __('Login'),
            '@system/registration'  => __('Registration'),
            '@system/profile'       => __('Profile'),
            '@system/resetpassword' => __('Password Reset')
        ];
    }
}
