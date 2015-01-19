<?php

namespace Pagekit\Tree\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Exception;
use Pagekit\Tree\Entity\Node;
use Pagekit\Tree\Event\NodeEditEvent;
use Pagekit\User\Entity\Role;

/**
 * @Access("tree: manage nodes", admin=true)
 */
class NodeController
{
    /**
     * @var array
     */
    protected $config;

    /**
     * Constructor.
     */
    public function __construct()
    {
        App::scripts('angular-resource');
        App::scripts('application', 'extensions/tree/assets/js/application.js', 'uikit');
        App::scripts('tree-application', 'extensions/tree/assets/js/tree.js', ['application', 'uikit-nestable']);
        App::scripts('tree-directives', 'extensions/tree/assets/js/directives.js', 'tree-application');
        App::scripts('tree-controllers', 'extensions/tree/assets/js/controllers.js', 'tree-application');

        App::on('kernel.view', function () {
            App::scripts('tree-config', sprintf('var %s = %s;', 'tree', json_encode($this->config)), [], 'string');
        });

        $this->config = ['config' => [
            'url'          => App::url()->base(),
            'route'        => App::url('@tree/node'),
            'url.template' => App::url('@tree/template')
        ]];
    }

    /**
     * @Route("/", methods="GET")
     * @Response("extensions/tree/views/admin/index.razr")
     */
    public function indexAction()
    {
        $this->config['data'] = [
            'types' => App::get('tree.types')->getTypes(),
            'nodes' => Node::findAll()
        ];

        $this->config['templates'] = [
            'tree.item' => App::view('extensions/tree/views/tmpl/item.razr')
        ];

        return ['head.title' => __('Nodes')];
    }

    /**
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     * @Route("/{type}", methods="GET")
     * @Request({"id": "int"})
     * @Response("extensions/tree/views/admin/edit.razr")
     */
    public function editAction($id = 0, $type = '')
    {
        try {

            if ($id === 0) {

                $node = new Node;
                $node->setType($type);

            } elseif (!$node = Node::find($id)) {
                throw new Exception(__('Invalid node id.'));
            }

            if (!isset(App::get('tree.types')[$node->getType()])) {
                throw new Exception(__('Invalid node type.'));
            }

            $this->config['data'] = [
                'type'  => App::get('tree.types')[$node->getType()],
                'node'  => $node,
                'roles' => Role::findAll()
            ];

            $this->config = App::trigger('tree.node.edit', new NodeEditEvent($node, $this->config))->getConfig();

        } catch (Exception $e) {

            App::message()->error($e->getMessage());

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

            if (!$node = Node::find($id)) {
                $node = new Node;
                unset($data['id']);
            }

//            if ($node->get('frontpage')) {
//                App::option()->set('system:app.frontpage', $node->)
//            }

            $node->save($data);

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

            if ($node = Node::find($id)) {
                $node->delete();
            }

        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'error' => true];
        }

        return ['message' => __('Success')];
    }

    /**
     * @Route("/bulk", methods="POST")
     * @Request({"nodes": "json"})
     * @Response("json")
     */
    public function bulkSaveAction($nodes = [])
    {
        foreach ($nodes as $data) {
            $this->saveAction($data, isset($data['id']) ? $data['id'] : 0);
        }

        return Node::findAll();
    }

    /**
     * @Route("/bulk", methods="DELETE")
     * @Request({"ids": "json"})
     * @Response("json")
     */
    public function bulkDeleteAction($ids = [])
    {
        foreach (array_filter($ids) as $id) {
            $this->deleteAction($id);
        }

        return Node::findAll();
    }
}
