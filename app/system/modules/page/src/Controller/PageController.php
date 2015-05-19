<?php

namespace Pagekit\Page\Controller;

use Pagekit\Application as App;
use Pagekit\Page\Entity\Page;

/**
 * @Access("site: manage pages")
 */
class PageController
{
    /**
     * @Route("/", methods="GET")
     */
    public function indexAction()
    {
        return array_values(Page::findAll());
    }

    /**
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     */
    public function getAction($id)
    {
        return Page::find($id);
    }
}
