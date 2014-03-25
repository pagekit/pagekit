<?php

namespace Pagekit\Menu\Link;

use Pagekit\System\Link\Route;

class Divider extends Route
{
    /**
     * {@inheritdoc}
     */
    public function getRoute()
    {
        return '!divider';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return __('Divider');
    }
}
