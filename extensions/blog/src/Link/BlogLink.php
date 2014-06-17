<?php

namespace Pagekit\Blog\Link;

use Pagekit\System\Link\Route;

class BlogLink extends Route
{
    /**
     * @{inheritdoc}
     */
    public function getRoute()
    {
        return '@blog/default';
    }

    /**
     * @{inheritdoc}
     */
    public function getLabel()
    {
        return __('Blog');
    }
}
