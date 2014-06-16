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
    public function renderForm($link, $params = [])
    {
        $pages = $this('db.em')->getRepository('Pagekit\Page\Entity\Page')->findAll();

        return $this('view')->render('page/admin/link/page.razr.php', compact('link', 'params', 'pages'));
    }
}
