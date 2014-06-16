<?php

namespace Pagekit\User\Link;

use Pagekit\Framework\ApplicationTrait;
use Pagekit\System\Link\LinkInterface;

class User implements LinkInterface
{
    use ApplicationTrait;

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
    public function renderForm($link, $params = [])
    {
        $routes = $this->getRoutes();

        return $this('view')->render('system/admin/user/link.razr.php', compact('link', 'params', 'routes'));
    }

    protected function getRoutes()
    {
        return [
            '@system/auth/login' => __('Login'),
            '@system/registration/index' => __('Registration'),
            '@system/profile/index' => __('Profile'),
            '@system/resetpassword/request' => __('Password Reset')
        ];
    }
}
