<?php

namespace Pagekit\Site\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Application\Exception;
use Pagekit\Site\Entity\Node;
use Pagekit\Site\Event\NodeEditEvent;
use Pagekit\User\Entity\Role;

/**
 * @Route("/site")
 * @Access("system: manage site", admin=true)
 */
class NodeController extends Controller
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
        App::scripts('application', 'extensions/system/app/application.js', 'uikit');
        App::scripts('site-application', 'extensions/system/app/site.js', ['application', 'uikit-nestable']);
        App::scripts('site-directives', 'extensions/system/app/directives.js', 'site-application');
        App::scripts('site-controllers', 'extensions/system/app/controllers.js', 'site-application');

        App::on('kernel.view', function () {
            App::scripts('site-config', sprintf('var %s = %s;', 'site', json_encode($this->config)), [], 'string');
        });

        $this->config = ['config' => [
            'url'          => App::url()->base(),
            'route'        => App::url('@system/node'),
            'url.template' => App::url('@system/template')
        ]];
    }

    /**
     * @Route("/", methods="GET")
     * @Response("extensions/system/views/admin/site/index.razr")
     */
    public function indexAction()
    {
        $this->config['data'] = [
            'types' => App::get('site.types')->getTypes(),
            'nodes' => Node::findAll()
        ];

        $this->config['templates'] = [
            'site.item' => App::view('extensions/system/views/tmpl/site.item.razr')
        ];

        return ['head.title' => __('Nodes')];
    }

    /**
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     * @Route("/{type}", methods="GET")
     * @Request({"id": "int"})
     * @Response("extensions/system/views/admin/site/edit.razr")
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

            if (!$type = App::get('site.types')[$node->getType()]) {
                throw new Exception(__('Invalid node type.'));
            }

            $this->config['data'] = [
                'type'  => $type,
                'node'  => $node,
                'roles' => Role::findAll()
            ];

            $this->config = App::trigger('site.node.edit', new NodeEditEvent($node, $this->config))->getConfig();

        } catch (Exception $e) {

            App::message()->error($e->getMessage());

            return $this->redirect('@system/node');
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
