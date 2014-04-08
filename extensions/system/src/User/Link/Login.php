<?php

namespace Pagekit\User\Link;

use Pagekit\System\Link\Route;

class Login extends Route
{
    /**
     * {@inheritdoc}
     */
    public function getRoute()
    {
        return '@system/auth/login';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return __('User Login');
    }
}
