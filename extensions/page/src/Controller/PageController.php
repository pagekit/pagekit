<?php

namespace Pagekit\Page\Controller;

use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\Page\Entity\Page;

/**
 * @Route("/page")
 * @Access("page: manage pages", admin=true)
 */
class PageController extends Controller
{
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
     * @Request({"filter": "array"})
     * @View("page/admin/pages/index.razr.php")
     */
    public function indexAction($filter = null)
    {
        if ($filter) {
            $this('session')->set('page.filter', $filter);
        } else {
            $filter = $this('session')->get('page.filter', array());
        }

        $query = $this->pages->query()->orderBy('title');

        if (isset($filter['status']) && is_numeric($filter['status'])) {
            $query->where(array('status' => intval($filter['status'])));
        }

        if (isset($filter['search']) && strlen($filter['search'])) {
            $query->where(function($query) use ($filter) {
                $query->orWhere(array('title LIKE :search', 'slug LIKE :search'), array('search' => "%{$filter['search']}%"));
            });
        }

        return array('head.title' => __('Pages'), 'pages' => $query->get(), 'statuses' => Page::getStatuses(), 'levels' => $this->levels->findAll(), 'filter' => $filter);
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

            $i = 2;

            while ($this->pages->where(array('slug = ?', 'id <> ?'), array($data['slug'], $id))->first()) {
                $data['slug'] .= "-$i";
            }

            $this->pages->save($page, $data);

            $this('message')->success($id ? __('Page saved.') : __('Page created.'));

            $id = $page->getId();

        } catch (Exception $e) {
            $this('message')->error($e->getMessage());
        }

        return $this->redirect(($id ? '@page/page/edit' : '@page/page/add'), compact('id'));
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

        $this('message')->success(_c('{0} No page deleted.|{1} Page deleted.|]1,Inf[ Pages deleted.', count($ids)));

        return $this->redirect('@page/page/index');
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
                $page->setStatus(Page::STATUS_DISABLED);
                $page->setSlug($page->getSlug().uniqid());
                $page->setTitle($page->getTitle().' - '.__('Copy'));

                $this->pages->save($page);
            }
        }

        return $this->redirect('@page/page/index');
    }

    /**
     * @Request({"status": "int", "ids": "int[]"})
     * @Token
     */
    public function statusAction($status, $ids = array())
    {
        try {

            foreach ($ids as $id) {
                if ($page = $this->pages->find($id) and $page->getStatus() != $status) {
                    $page->setStatus($status);
                    $this->pages->save($page);
                }
            }

        } catch (Exception $e) {
            $this('message')->error($e->getMessage());
        }

        return $this->redirect('@page/page/index');
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
