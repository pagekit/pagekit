<?php

namespace Pagekit\User\Link;

use Pagekit\System\Link\Route;

class Registration extends Route
{
    /**
     * {@inheritdoc}
     */
    public function getRoute()
    {
        return '@system/registration/index';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return __('User Registration');
    }
}
