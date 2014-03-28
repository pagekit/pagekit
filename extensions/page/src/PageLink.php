<?php

namespace Pagekit\Page;

use Pagekit\System\Link\Link;

class PageLink extends Link
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
    public function renderForm()
    {
        $pages = $this('db.em')->getRepository('Pagekit\Page\Entity\Page')->findAll();

        return $this('view')->render('page/admin/link.razr.php', array('route' => $this->getRoute(), 'pages' => $pages));
    }
}
