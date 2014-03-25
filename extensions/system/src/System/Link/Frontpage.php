<?php

namespace Pagekit\System\Link;

class Frontpage extends Route
{
    /**
     * {@inheritdoc}
     */
    public function getRoute()
    {
        return '@frontpage';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return __('Frontpage');
    }
}
