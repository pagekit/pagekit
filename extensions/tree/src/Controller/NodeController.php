<?php

namespace Pagekit\Tree\Controller;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\Tree\Entity\Node;

/**
 * @Access("tree: manage nodes", admin=true)
 */
class NodeController extends Controller
{
    /**
     * @var Repository
     */
    protected $nodes;

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
        $this->nodes = $this['db.em']->getRepository('Pagekit\Tree\Entity\Node');
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
                'route'  => $this['url']->route('@tree/node'),
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
        $this->config['config']['nodes'] = $this->nodes->findAll();

        return ['head.title' => __('Nodes')];
    }

    /**
     * @Route("/add", methods="GET")
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     * @Request({"id": "int"})
     * @Response("extension://tree/views/admin/edit.razr")
     */
    public function editAction($id = 0)
    {
        try {

            if ($id === 0) {
                $node = new Node;
            } elseif (!$node = $this->nodes->find($id)) {
                throw new Exception(__('Invalid node id.'));
            }

            $this->config['config']['node'] = $node;

        } catch (Exception $e) {

            $this['message']->error($e->getMessage());

            return $this->redirect('@tree/tree');
        }

        return ['head.title' => $node->getId() ? __('Edit Node') : __('Add Node')];
    }

    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
     * @Request({"node": "array", "id": "int"})
     * @Response("json")
     */
    public function saveAction($data, $id = 0)
    {
        try {

            if (!$node = $this->nodes->find($id)) {
                $node = new Node;
                unset($data['id']);
            }

            $this->nodes->save($node, $data);

            return $node;

        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'error' => true];
        }
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
     * @Request({"id": "int"})
     * @Response("json")
     */
    public function deleteAction($id)
    {
        try {

            if ($node = $this->nodes->find($id)) {
                $this->nodes->delete($node);
            }

        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'error' => true];
        }

        return $this->nodes->findAll();
    }

    /**
     * @Route("/reorder", methods="POST")
     * @Request({"nodes": "json"})
     * @Response("json")
     */
    public function reorderAction($datas = [])
    {
        $nodes = $this->nodes->findAll();

        foreach ($datas as $data) {
            if (isset($nodes[$data['id']])) {
                $this->nodes->save($nodes[$data['id']], $data);
            }
        }

        return $nodes;
    }
}
