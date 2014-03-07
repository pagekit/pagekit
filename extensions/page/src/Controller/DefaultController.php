<?php

namespace Pagekit\Page\Controller;

use Pagekit\Framework\Controller\Controller;
use Pagekit\System\Event\ContentEvent;

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

        if (!$this('users')->hasAccess($page->getAccessId())) {
            return $this('response')->create(__('Unable to access this page!'), 403);
        }

        if (0 !== strpos($this->url('@page/slug', array('slug' => $page->getSlug())), $this('request')->get)) {

        }

        $this('events')->trigger('content.plugins', $event = new ContentEvent($page->getContent(), compact('page')));

        return array('meta.title' => __($page->getTitle()), 'page' => $page, 'content' => $event->getContent());
    }
}
