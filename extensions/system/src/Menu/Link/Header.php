<?php

namespace Pagekit\Menu\Link;

use Pagekit\System\Link\Route;

class Header extends Route
{
    /**
     * {@inheritdoc}
     */
    public function getRoute()
    {
        return '!menu-header';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return __('Menu Header');
    }
}