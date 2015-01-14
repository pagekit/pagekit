<?php

namespace Pagekit\Page\Controller;

use Pagekit\Framework\Controller\Controller;
use Pagekit\Page\Entity\Page;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/")
 */
class SiteController extends Controller
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

        $page->setContent($this['content']->applyPlugins($page->getContent(), ['page' => $page, 'markdown' => $page->get('markdown')]));

        return ['head.title' => __($page->getTitle()), 'page' => $page];
    }
}
