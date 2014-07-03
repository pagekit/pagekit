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

        return $this['view']->render('system/admin/link/system.razr', compact('link', 'params', 'routes'));
    }

    protected function getRoutes()
    {
        return [
            '/'                     => __('Frontpage'),
            '@system/auth/login'    => __('Login'),
            '@system/registration'  => __('Registration'),
            '@system/profile'       => __('Profile'),
            '@system/resetpassword' => __('Password Reset')
        ];
    }
}
