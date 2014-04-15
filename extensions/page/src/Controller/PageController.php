<?php

namespace Pagekit\Page\Controller;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\Page\Entity\Page;

/**
 * @Route("/page")
 * @Access("page: manage pages", admin=true)
 */
class PageController extends Controller
{
    const PAGES_PER_PAGE = 20;

    /**
     * @var Repository
     */
    protected $pages;

    /**
     * @var Repository
     */
    protected $levels;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->pages  = $this('db.em')->getRepository('Pagekit\Page\Entity\Page');
        $this->levels = $this('users')->getAccessLevelRepository();
    }

    /**
     * @Request({"filter": "array", "page":"int", "rows":"bool"})
     * @View("page/admin/pages/index.razr.php")
     */
    public function indexAction($filter = null, $page = 0, $rows = false)
    {
        if ($filter) {
            $this('session')->set('page.filter', $filter);
        } else {
            $filter = $this('session')->get('page.filter', array());
        }

        $query = $this->pages->query();

        if (isset($filter['status']) && is_numeric($filter['status'])) {
            $query->where(array('status' => intval($filter['status'])));
        }

        if (isset($filter['search']) && strlen($filter['search'])) {
            $query->where(function($query) use ($filter) {
                $query->orWhere(array('title LIKE :search', 'slug LIKE :search'), array('search' => "%{$filter['search']}%"));
            });
        }

        $limit = self::PAGES_PER_PAGE;
        $total = ceil($query->count() / $limit);
        $page  = min($total, max($page, 0));

        $query->offset($page * $limit)->limit($limit)->orderBy('title');

        if ($this('request')->isXmlHttpRequest()) {
            return $this('response')->json(array(
                'table' => $this('view')->render('view://page/admin/pages/table.razr.php', array('pages' => $query->get(), 'levels' => $this->levels->findAll())),
                'total' => $total
            ));
        }

        return array('head.title' => __('Pages'), 'pages' => $query->get(), 'statuses' => Page::getStatuses(), 'levels' => $this->levels->findAll(), 'filter' => $filter, 'total' => $total);
    }

    /**
     * @View("page/admin/pages/edit.razr.php")
     */
    public function addAction()
    {
        return array('head.title' => __('Add Page'), 'page' => new Page, 'statuses' => Page::getStatuses(), 'levels' => $this->levels->findAll());
    }

    /**
     * @Request({"id": "int"})
     * @View("page/admin/pages/edit.razr.php")
     */
    public function editAction($id)
    {
        try {

            if (!$page = $this->pages->find($id)) {
                throw new Exception(__('Invalid page id.'));
            }

        } catch (Exception $e) {

            $this('message')->error($e->getMessage());

            return $this->redirect('@page/page/index');
        }

        return array('head.title' => __('Edit Page'), 'page' => $page, 'statuses' => Page::getStatuses(), 'levels' => $this->levels->findAll());
    }

    /**
     * @Request({"id": "int", "page": "array"})
     * @Token
     */
    public function saveAction($id, $data)
    {
        try {

            if (!$page = $this->pages->find($id)) {
                $page = new Page;
            }

            if (!$data['slug'] = $this->slugify($data['slug'] ?: $data['title'])) {
                throw new Exception('Invalid slug.');
            }

            $this->pages->save($page, $data);

            $response = array('message' => $id ? __('Page saved.') : __('Page created.'), 'id' => $page->getId());

        } catch (Exception $e) {
            $response = array('message' => $e->getMessage(), 'error' => true);
        }

        return $this('response')->json($response);
    }

    /**
     * @Request({"ids": "int[]"})
     * @Token
     */
    public function deleteAction($ids = array())
    {
        foreach ($ids as $id) {
            if ($page = $this->pages->find($id)) {
                $this->pages->delete($page);
            }
        }

        return $this('response')->json(array('message' => _c('{0} No page deleted.|{1} Page deleted.|]1,Inf[ Pages deleted.', count($ids))));
    }

    /**
     * @Request({"ids": "int[]"})
     * @Token
     */
    public function copyAction($ids = array())
    {
        foreach ($ids as $id) {
            if ($page = $this->pages->find((int) $id)) {

                $page = clone $page;
                $page->setId(null);
                $page->setStatus(Page::STATUS_UNPUBLISHED);
                $page->setSlug($page->getSlug());
                $page->setTitle(__('%title% (Copy)', array('%title%' => $page->getTitle())));

                $this->pages->save($page);
            }
        }

        return $this('response')->json(array('message' => _c('{0} No page copied.|{1} Page copied.|]1,Inf[ Pages copied.', count($ids))));
    }

    /**
     * @Request({"status": "int", "ids": "int[]"})
     * @Token
     */
    public function statusAction($status, $ids = array())
    {
        foreach ($ids as $id) {
            if ($page = $this->pages->find($id) and $page->getStatus() != $status) {
                $page->setStatus($status);
                $this->pages->save($page);
            }
        }

        if ($status == Page::STATUS_PUBLISHED) {
            $message = _c('{0} No page published.|{1} Page published.|]1,Inf[ Pages published.', count($ids));
        } else {
            $message = _c('{0} No page unpublished.|{1} Page unpublished.|]1,Inf[ Pages unpublished.', count($ids));
        }

        return $this('response')->json(compact('message'));
    }

    /**
     * @Request({"slug", "id": "int"})
     * @Token
     */
    public function getSlugAction($slug, $id = 0)
    {
        $slug = $this->slugify($slug);
        $i = 2;
        while ($this->pages->query()->where('slug = ?', array($slug))->where(function($query) use($id) { if ($id) $query->where('id <> ?', array($id)); })->first()) {
            $slug = preg_replace('/-\d+$/', '', $slug).'-'.$i++;
        }

        return $this('response')->json($slug);
    }

    protected function slugify($slug)
    {
        $slug = preg_replace('/\xE3\x80\x80/', ' ', $slug);
        $slug = str_replace('-', ' ', $slug);
        $slug = preg_replace('#[:\#\*"@+=;!><&\.%()\]\/\'\\\\|\[]#', "\x20", $slug);
        $slug = str_replace('?', '', $slug);
        $slug = trim(mb_strtolower($slug, 'UTF-8'));
        $slug = preg_replace('#\x20+#', '-', $slug);

        return $slug;
    }
}
