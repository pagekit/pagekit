<?php

namespace Pagekit\Page;

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
        $pages = $this['db.em']->getRepository('Pagekit\Page\Entity\Page')->findAll();

        return $this['view']->render('extension://page/views/admin/link/page.razr', compact('link', 'params', 'pages'));
    }
}
