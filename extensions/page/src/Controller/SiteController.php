<?php

namespace Pagekit\Page\Controller;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Page\Entity\Page;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/page")
 */
class SiteController extends Controller
{
    /**
     * @var Repository
     */
    protected $pages;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->pages = $this['db.em']->getRepository('Pagekit\Page\Entity\Page');
    }

    /**
     * @Route("/{id}", name="@page/id", requirements={"id"="\d+"})
     * @Response("extension://page/views/index.razr")
     */
    public function indexAction($id = 0)
    {
        if (!$page = $this->pages->where(compact('id'))->where(['status' => Page::STATUS_PUBLISHED])->first()) {
            throw new NotFoundHttpException(__('Page not found!'));
        }

        if (!$page->hasAccess($this['user'])) {

            if (!$this['user']->isAuthenticated()) {
                return $this->redirect('@system/auth/login', ['redirect' => $this['url']->current()]);
            }

            throw new AccessDeniedHttpException(__('Unable to access this page!'));
        }

        $page->setContent($this['content']->applyPlugins($page->getContent(), ['page' => $page, 'markdown' => $page->get('markdown')]));

        return ['head.title' => __($page->getTitle()), 'page' => $page];
    }
}
