<?php

namespace Pagekit\Page\Controller;

use Pagekit\Framework\Application as App;
use Pagekit\Page\Entity\Page;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/")
 */
class SiteController
{
    /**
     * @Route("/{id}", name="id")
     * @Request({"id" : "int"})
     * @Response("extensions/page/views/index.razr")
     */
    public function indexAction($id = 0)
    {
        if (!$page = Page::find($id)) {
            throw new NotFoundHttpException(__('Page not found!'));
        }

        $page->setContent(App::content()->applyPlugins($page->getContent(), ['page' => $page, 'markdown' => $page->get('markdown')]));

        return ['head.title' => __($page->getTitle()), 'page' => $page];
    }
}
