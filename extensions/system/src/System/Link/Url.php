<?php

namespace Pagekit\System\Link;

use Pagekit\Framework\ApplicationAware;

class Url extends ApplicationAware implements LinkInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRoute()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return __('Url');
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm()
    {
        return $this('view')->render('system/admin/links/url.razr.php');
    }
}
