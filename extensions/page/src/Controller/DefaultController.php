<?php

namespace Pagekit\Page\Controller;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Page\Entity\Page;

/**
 * @Route("/page")
 */
class DefaultController extends Controller
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
        $this->pages = $this('db.em')->getRepository('Pagekit\Page\Entity\Page');
    }

    /**
     * @Route("/{id}", name="@page/id", requirements={"id"="\d+"})
     * @Route("/{slug}", name="@page/slug", defaults={"_main_route" = "@page/id"})
     * @View("page/index.razr.php")
     */
    public function indexAction($id = 0, $slug = '')
    {
        if (!$page = $this->pages->where(compact($slug ? 'slug' : 'id'))->where(array('status' => Page::STATUS_PUBLISHED))->first()) {
            return $this('response')->create(__('Page not found!'), 404);
        }

        if (!$this('users')->checkAccessLevel($page->getAccessId())) {
            return $this('response')->create(__('Unable to access this page!'), 403);
        }

        $page->setContent($this('content')->applyPlugins($page->getContent(), array('page' => $page, 'markdown' => $page->get('markdown'))));

        return array('head.title' => __($page->getTitle()), 'page' => $page);
    }
}
