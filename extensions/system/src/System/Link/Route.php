<?php

namespace Pagekit\System\Link;

abstract class Route extends Link
{
    /**
     * {@inheritdoc}
     */
    public function renderForm()
    {
        return $this('view')->render('system/admin/links/route.razr.php', array('route' => $this->getRoute()));
    }
}
