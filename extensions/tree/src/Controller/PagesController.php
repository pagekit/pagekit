<?php

namespace Pagekit\Tree\Controller;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\Tree\Entity\Page;

/**
 * @Access("tree: manage pages", admin=true)
 */
class PagesController extends Controller
{
    /**
     * @var Repository
     */
    protected $pages;

    /**
     * @var Repository
     */
    protected $roles;

    /**
     * @var array
     */
    protected $config;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->pages = $this['db.em']->getRepository('Pagekit\Tree\Entity\Page');
        $this->roles = $this['users']->getRoleRepository();

        $this['view.scripts']->queue('angular', 'extension://tree/assets/angular/angular.min.js', 'jquery');
        $this['view.scripts']->queue('angular-resource', 'extension://tree/assets/angular-resource/angular-resource.min.js', 'angular');
        $this['view.scripts']->queue('application', 'extension://tree/assets/js/application.js', 'uikit');
        $this['view.scripts']->queue('tree-application', 'extension://tree/assets/js/tree.js', ['application', 'uikit-nestable']);
        $this['view.scripts']->queue('tree-directives', 'extension://tree/assets/js/directives.js', 'tree-application');
        $this['view.scripts']->queue('tree-controllers', 'extension://tree/assets/js/controllers.js', 'tree-application');

        $this->getApplication()->on('kernel.view', function() {
            $this['view.scripts']->queue('tree-config', sprintf('var %s = %s;', 'tree', json_encode($this->config)), [], 'string');
        });

        $this->config = [
            'config' => [
                'url'    => $this['url']->base(),
                'route'  => $this['url']->route('@tree/pages'),
                'mounts' => $this['mounts']
            ]
        ];
    }

    /**
     * @Route("/", methods="GET")
     * @Response("extension://tree/views/admin/index.razr")
     */
    public function indexAction()
    {
        $this->config['config']['pages'] = $this->pages->findAll();

        return ['head.title' => __('Pages')];
    }

    /**
     * @Route("/add", methods="GET")
     * @Route("/{id}", methods="GET", requirements={"id": "\d+"})
     * @Request({"id": "int"})
     * @Response("extension://tree/views/admin/edit.razr")
     */
    public function editAction($id = 0)
    {
        try {

            if ($id === 0) {
                $page = new Page;
            } elseif (!$page = $this->pages->find($id)) {
                throw new Exception(__('Invalid page id.'));
            }

            $this->config['config']['page'] = $page;

        } catch (Exception $e) {

            $this['message']->error($e->getMessage());

            return $this->redirect('@tree/tree');
        }

        return ['head.title' => $page->getId() ? __('Edit Page') : __('Add Page')];
    }

    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id": "\d+"})
     * @Request({"page": "array", "id": "int"})
     * @Response("json")
     */
    public function saveAction($data, $id = 0)
    {
        try {

            if (!$page = $this->pages->find($id)) {
                $page = new Page;
                unset($data['id']);
            }

            $this->pages->save($page, $data);

            return $page;

        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'error' => true];
        }
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id": "\d+"})
     * @Request({"id": "int"})
     * @Response("json")
     */
    public function deleteAction($id)
    {
        try {

            if ($page = $this->pages->find($id)) {
                $this->pages->delete($page);
            }

        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'error' => true];
        }

        return $this->pages->findAll();
    }

    /**
     * @Route("/reorder", methods="POST")
     * @Request({"pages": "json"})
     * @Response("json")
     */
    public function reorderAction($datas = [])
    {
        $pages = $this->pages->findAll();

        foreach ($datas as $data) {
            if (isset($pages[$data['id']])) {
                $this->pages->save($pages[$data['id']], $data);
            }
        }

        return $pages;
    }
}
