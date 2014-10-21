<?php

namespace Pagekit\System\Link;

class System extends Link
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'system';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return __('System');
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

        if (in_array($context, ['frontpage', 'urlalias'])) {
            unset($routes['/']);
        }

        return $this['view']->render('extension://system/views/admin/link/system.razr', compact('link', 'params', 'routes'));
    }

    protected function getRoutes()
    {
        return [
            '/'                     => __('Frontpage'),
            '@system/auth/login'    => __('User Login'),
            '@system/auth/logout'   => __('User Logout'),
            '@system/registration'  => __('User Registration'),
            '@system/profile'       => __('User Profile'),
            '@system/resetpassword' => __('User Password Reset')
        ];
    }
}
