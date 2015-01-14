<?php

namespace Pagekit\Page;

use Pagekit\Page\Entity\Page;
use Pagekit\System\Link\Route;

class PageLink extends Route
{
    /**
     * @{inheritdoc}
     */
    public function getRoute()
    {
        return '@page/id';
    }

    /**
     * @{inheritdoc}
     */
    public function getLabel()
    {
        return __('Page');
    }

    /**
     * @{inheritdoc}
     */
    public function renderForm($link, $params = [], $context = '')
    {
        $pages = Page::findAll();

        return $this['view']->render('extensions/page/views/admin/link/page.razr', compact('link', 'params', 'pages'));
    }
}
