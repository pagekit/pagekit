<?php

namespace Pagekit\Site\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Application\Exception;
use Pagekit\Site\Entity\Node;
use Pagekit\Site\Event\ConfigEvent;
use Pagekit\User\Entity\Role;

/**
 * @Route("/site")
 * @Access("system: manage site", admin=true)
 */
class NodeController extends Controller
{
    /**
     * @Route("/", methods="GET")
     * @Response("extensions/system/views/admin/site/index.php")
     */
    public function indexAction()
    {
        $config = App::trigger('site.config', new ConfigEvent([
            'config'    => [
                'url'          => App::url()->base(),
                'route'        => App::url('@system/node'),
                'url.template' => App::url('@system/template'),
                'csrf'         => App::csrf()->generate()
            ],
            'data'   => [
                'types' => App::get('site.types')->getTypes(),
                'roles' => Role::findAll()
            ],
            'templates' => [
                'site.edit' => App::view('extensions/system/views/tmpl/site.edit.php'),
                'site.item' => App::view('extensions/system/views/tmpl/site.item.php'),
                'site.list' => App::view('extensions/system/views/tmpl/site.list.php')
            ]
        ]))->getConfig();

        App::on('kernel.view', function () use ($config) {
            App::scripts('site-config', sprintf('var %s = %s;', 'site', json_encode($config)), [], 'string');
        });

        return ['head.title' => __('Nodes')];
    }

    /**
     * @Route("/node/", methods="GET")
     * @Route("/node/{id}", methods="GET", requirements={"id"="\d+"})
     * @Response("json")
     */
    public function getAction($id = 0)
    {
        return $id ? Node::find($id) : Node::findAll();
    }

    /**
     * @Route("/node/", methods="POST")
     * @Route("/node/{id}", methods="POST", requirements={"id"="\d+"})
     * @Request({"node": "array", "id": "int"}, csrf=true)
     * @Response("json")
     */
    public function saveAction($data, $id = 0)
    {
        try {

            if (!$node = Node::find($id)) {
                $node = new Node;
                unset($data['id']);
            }

            $node->save($data);

            return $node;

        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'error' => true];
        }
    }

    /**
     * @Route("/node/{id}", methods="DELETE", requirements={"id"="\d+"})
     * @Request({"id": "int"}, csrf=true)
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
     * @Route("/node/bulk", methods="POST")
     * @Request({"nodes": "json"}, csrf=true)
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
     * @Route("/node/bulk", methods="DELETE")
     * @Request({"ids": "json"}, csrf=true)
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
