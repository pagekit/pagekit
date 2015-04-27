<?php

namespace Pagekit\Site\Controller;

use Pagekit\Application as App;
use Pagekit\Site\Entity\Node;

/**
 * @Access("site: manage site")
 */
class NodeController
{
    /**
     * @Route("/", methods="GET")
     */
    public function indexAction()
    {
        return array_values(Node::findAll());
    }

    /**
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     */
    public function getAction($id)
    {
        return Node::find($id);
    }

    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
     * @Request({"node": "array", "id": "int", "frontpage": "bool"}, csrf=true)
     */
    public function saveAction($data, $id = 0, $frontpage = false)
    {
        if (!$node = Node::find($id)) {
            $node = new Node;
            unset($data['id']);
        }

        $node->save($data);

        if ($frontpage && $type = App::module('system/site')->getType($node->getType())) {
            $config = App::config('system/site', []);
            $config['frontpage_node'] = $node->getId();
            $config['frontpage'] = $type->getLink($node);
            App::config()->set('system/site', $config, true);
        }

        return $node;
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
     * @Request({"id": "int"}, csrf=true)
     */
    public function deleteAction($id)
    {
        if ($node = Node::find($id)) {
            $node->delete();
        }

        return 'success';
    }

    /**
     * @Route("/bulk", methods="POST")
     * @Request({"nodes": "array"}, csrf=true)
     */
    public function bulkSaveAction($nodes = [])
    {
        foreach ($nodes as $data) {
            $this->saveAction($data, isset($data['id']) ? $data['id'] : 0);
        }

        return 'success';
    }

    /**
     * @Route("/bulk", methods="DELETE")
     * @Request({"ids": "array"}, csrf=true)
     */
    public function bulkDeleteAction($ids = [])
    {
        foreach (array_filter($ids) as $id) {
            $this->deleteAction($id);
        }

        return 'success';
    }

    /**
     * @Route("/updateOrder", methods="POST")
     * @Request({"menu", "nodes": "array"}, csrf=true)
     */
    public function updateOrderAction($menu, $nodes = []) {
        foreach ($nodes as $data) {
            if ($node = Node::find($data['id'])) {

                $node->setParentId($data['parent_id']);
                $node->setPriority($data['order']);
                $node->setMenu($menu);

                $node->save();
            }
        }

        return 'success';
    }
}
