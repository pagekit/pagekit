<?php

namespace Pagekit\Hello;

use Pagekit\System\Link\Link;

class HelloLink extends Link
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
    public function renderForm()
    {
        return $this('view')->render('hello/admin/link.razr.php', array('route' => $this->getRoute()));
    }
}