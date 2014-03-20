<?php

namespace Pagekit\Hello;

use Pagekit\Framework\ApplicationAware;
use Pagekit\System\Link\LinkInterface;

class HelloLink extends ApplicationAware implements LinkInterface
{
    /**
     * @{inheritdoc}
     */
    public function getRoute()
    {
        return '@hello/default/index_1';
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