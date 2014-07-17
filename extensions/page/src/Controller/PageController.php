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
    protected $roles;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->pages = $this['db.em']->getRepository('Pagekit\Page\Entity\Page');
        $this->roles = $this['users']->getRoleRepository();
    }

    /**
     * @Request({"filter": "array", "page":"int"})
     * @Response("extension://page/views/admin/pages/index.razr")
     */
    public function indexAction($filter = null, $page = 0)
    {
        if ($filter) {
            $this['session']->set('page.filter', $filter);
        } else {
            $filter = $this['session']->get('page.filter', []);
        }

        $query = $this->pages->query();

        if (isset($filter['status']) && is_numeric($filter['status'])) {
            $query->where(['status' => intval($filter['status'])]);
        }

        if (isset($filter['search']) && strlen($filter['search'])) {
            $query->where(function($query) use ($filter) {
                $query->orWhere('title LIKE :search', ['search' => "%{$filter['search']}%"]);
            });
        }

        $limit = self::PAGES_PER_PAGE;
        $count = $query->count();
        $total = ceil($count / $limit);
        $page  = max(0, min($total - 1, $page));

        $query->offset($page * $limit)->limit($limit)->orderBy('title');

        if ($this['request']->isXmlHttpRequest()) {
            return $this['response']->json([
                'table' => $this['view']->render('extension://page/views/admin/pages/table.razr', ['count' => $count, 'pages' => $query->get(), 'roles' => $this->roles->findAll()]),
                'total' => $total
            ]);
        }

        return ['head.title' => __('Pages'), 'pages' => $query->get(), 'statuses' => Page::getStatuses(), 'filter' => $filter, 'total' => $total, 'count' => $count];
    }

    /**
     * @Response("extension://page/views/admin/pages/edit.razr")
     */
    public function addAction()
    {
        return ['head.title' => __('Add Page'), 'page' => new Page, 'statuses' => Page::getStatuses(), 'roles' => $this->roles->findAll()];
    }

    /**
     * @Request({"id": "int"})
     * @Response("extension://page/views/admin/pages/edit.razr")
     */
    public function editAction($id)
    {
        try {

            if (!$page = $this->pages->find($id)) {
                throw new Exception(__('Invalid page id.'));
            }

        } catch (Exception $e) {

            $this['message']->error($e->getMessage());

            return $this->redirect('@page/page');
        }

        return ['head.title' => __('Edit Page'), 'page' => $page, 'statuses' => Page::getStatuses(), 'roles' => $this->roles->findAll()];
    }

    /**
     * @Request({"id": "int", "page": "array"}, csrf=true)
     */
    public function saveAction($id, $data)
    {
        try {

            if (!$page = $this->pages->find($id)) {
                $page = new Page;
            }

            if ($this->pages->where(['url = ?', 'id <> ?'], [$data['url'], $page->getId()])->first()) {
                throw new Exception(__('Page Url not available.'));
            }

            $data['data'] = array_merge(['title' => 0, 'markdown' => 0], isset($data['data']) ? $data['data'] : []);

            $this->pages->save($page, $data);

            $response = ['message' => $id ? __('Page saved.') : __('Page created.'), 'id' => $page->getId()];

        } catch (Exception $e) {
            $response = ['message' => $e->getMessage(), 'error' => true];
        }

        return $this['response']->json($response);
    }

    /**
     * @Request({"ids": "int[]"}, csrf=true)
     * @Response("json")
     */
    public function deleteAction($ids = [])
    {
        foreach ($ids as $id) {
            if ($page = $this->pages->find($id)) {
                $this->pages->delete($page);
            }
        }

        return ['message' => _c('{0} No page deleted.|{1} Page deleted.|]1,Inf[ Pages deleted.', count($ids))];
    }

    /**
     * @Request({"ids": "int[]"}, csrf=true)
     * @Response("json")
     */
    public function copyAction($ids = [])
    {
        foreach ($ids as $id) {
            if ($page = $this->pages->find((int) $id)) {

                $page = clone $page;
                $page->setId(null);
                $page->setUrl('');
                $page->setStatus(Page::STATUS_UNPUBLISHED);
                $page->setTitle(__('%title% (Copy)', ['%title%' => $page->getTitle()]));

                $this->pages->save($page);
            }
        }

        return ['message' => _c('{0} No page copied.|{1} Page copied.|]1,Inf[ Pages copied.', count($ids))];
    }

    /**
     * @Request({"status": "int", "ids": "int[]"}, csrf=true)
     * @Response("json")
     */
    public function statusAction($status, $ids = [])
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

        return compact('message');
    }
}
