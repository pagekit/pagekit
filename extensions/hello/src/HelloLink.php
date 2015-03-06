<?php

namespace Pagekit\Hello;

use Pagekit\Application as App;
use Pagekit\System\Link\Route;

class HelloLink extends Route
{
    /**
     * @{inheritdoc}
     */
    public function getRoute()
    {
        return '@hello/greet/name';
    }

    /**
     * @{inheritdoc}
     */
    public function getLabel()
    {
        return __('Hello World');
    }

    /**
     * @{inheritdoc}
     */
    public function renderForm($link, $params = [], $context = '')
    {
        return App::tmpl('extensions/hello/views/admin/link.razr', compact('link', 'params'));
    }
}
