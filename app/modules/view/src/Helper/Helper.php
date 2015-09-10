<?php

namespace Pagekit\View\Helper;

use Pagekit\View\View;

abstract class Helper implements HelperInterface
{
    /**
     * @var View
     */
    protected $view;

    /**
     * {@inheritdoc}
     */
    public function register(View $view)
    {
        $this->view = $view;
    }
}
