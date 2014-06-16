<?php

namespace Pagekit\Hello;

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
    public function renderForm($link, $params = [])
    {
        return $this('view')->render('hello/admin/link.razr.php', compact('link', 'params'));
    }
}