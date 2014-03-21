<?php

namespace Pagekit\Page\Controller;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Content\Event\ContentEvent;
use Pagekit\Framework\Controller\Controller;

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
        if (!$page = $this->pages->where(compact($slug ? 'slug' : 'id'))->first()) {
            return $this('response')->create(__('Page not found!'), 404);
        }

        if (!$this('users')->checkAccessLevel($page->getAccessId())) {
            return $this('response')->create(__('Unable to access this page!'), 403);
        }

        $this('events')->trigger('content.plugins', $event = new ContentEvent($page->getContent(), compact('page')));

        return array('head.title' => __($page->getTitle()), 'page' => $page, 'content' => $event->getContent());
    }
}
