<?php

namespace Pagekit\Tree\Controller;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\Framework\Event\Event;
use Pagekit\Tree\Entity\Node;
use Pagekit\Tree\Event\NodeEditEvent;

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

        $this->getApplication()->on('kernel.view', function () {
            $this['view.scripts']->queue('tree-config', sprintf('var %s = %s;', 'tree', json_encode($this->config)), [], 'string');
        });

        $this->config = ['config' => [
            'url'          => $this['url']->base(),
            'route'        => $this['url']->route('@tree/node'),
            'url.template' => $this['url']->route('@tree/template')
        ]];
    }

    /**
     * @Route("/", methods="GET")
     * @Response("extension://tree/views/admin/index.razr")
     */
    public function indexAction()
    {
        $this->config['config']['types'] = $this['tree.types'];
        $this->config['config']['nodes'] = $this->nodes->findAll();
        $this->config['templates']       = [
            'tree.item' => $this['view']->render('extension://tree/views/tmpl/item.razr')
        ];

        return ['head.title' => __('Nodes')];
    }

    /**
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     * @Route("/{type}", methods="GET")
     * @Request({"id": "int"})
     * @Response("extension://tree/views/admin/edit.razr")
     */
    public function editAction($id = 0, $type = '')
    {
        try {

            if ($id === 0) {

                $node = new Node;
                $node->setType($type);

            } elseif (!$node = $this->nodes->find($id)) {
                throw new Exception(__('Invalid node id.'));
            }

            if (!isset($this['tree.types'][$node->getType()])) {
                throw new Exception(__('Invalid node type.'));
            }

            $this->config = $this['events']->dispatch('tree.node.edit', new NodeEditEvent($node, $this->config))->getConfig();

            $this->config['config']['node'] = $node;
            $this->config['config']['type'] = $this['tree.types'][$node->getType()];

        } catch (Exception $e) {

            $this['message']->error($e->getMessage());

            return $this->redirect('@tree/node');
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
