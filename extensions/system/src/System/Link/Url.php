<?php

namespace Pagekit\System\Link;

class Url extends Link
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
