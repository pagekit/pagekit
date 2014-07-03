<?php

namespace Pagekit\System\Link;

class Frontpage extends Route
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
    public function renderForm($link, $params = [])
    {
        return parent::renderForm('/');
    }
}
