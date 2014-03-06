<?php

namespace Pagekit\System\Link;

use Pagekit\Framework\ApplicationAware;

class Frontpage extends ApplicationAware implements LinkInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRoute()
    {
        return '/';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return __('Frontpage');
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm()
    {
        return $this('view')->render('system/admin/links/frontpage.razr.php');
    }
}
