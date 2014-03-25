<?php

namespace Pagekit\System\Link;

use Pagekit\Framework\ApplicationAware;

abstract class Route extends ApplicationAware implements LinkInterface
{
    /**
     * {@inheritdoc}
     */
    public function renderForm()
    {
        return $this('view')->render('system/admin/links/route.razr.php', array('route' => $this->getRoute()));
    }
}
