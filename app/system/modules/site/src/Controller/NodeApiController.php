<?php

namespace Pagekit\Site\Controller;

use Pagekit\Application as App;
use Pagekit\Site\Model\Node;

/**
 * @Access("site: manage site")
 */
class NodeApiController
{
    /**
     * @Route("/", methods="GET")
     * @Request({"menu"})
     */
    public function indexAction($menu = false)
    {
        $query = Node::query();

        if (is_string($menu)) {
            $query->where(['menu' => $menu]);
        }

        return array_values($query->get());
    }

    /**
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     */
    public function getAction($id)
    {
        if (!$node = Node::find($id)) {
            App::abort(404, __('Node not found.'));
        }

        return $node;
    }

    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
     * @Request({"node": "array", "id": "int"}, csrf=true)
     */
    public function saveAction($data, $id = 0)
    {
        if (!$node = Node::find($id)) {
            $node = Node::create();
            unset($data['id']);
        }

        if (!$data['slug'] = App::filter($data['slug'] ?: $data['title'], 'slugify')) {
            App::abort(400, __('Invalid slug.'));
        }

        $node->save($data);

        return ['message' => 'success', 'node' => $node];
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
     * @Request({"id": "int"}, csrf=true)
     */
    public function deleteAction($id)
    {
        if ($node = Node::find($id)) {

            if ($type = App::module('system/site')->getType($node->type) and isset($type['protected']) and $type['protected']) {
                App::abort(400, __('Invalid type.'));
            }

            $node->delete();
        }

        return ['message' => 'success'];
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

        return ['message' => 'success'];
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

        return ['message' => 'success'];
    }

    /**
     * @Route("/updateOrder", methods="POST")
     * @Request({"menu", "nodes": "array"}, csrf=true)
     */
    public function updateOrderAction($menu, $nodes = [])
    {
        foreach ($nodes as $data) {

            if ($node = Node::find($data['id'])) {

                $node->priority  = $data['order'];
                $node->menu      = $menu;
                $node->parent_id = $data['parent_id'] ?: 0;

                $node->save();
            }
        }

        return ['message' => 'success'];
    }

    /**
     * @Route("/frontpage", methods="POST")
     * @Request({"id": "int"}, csrf=true)
     */
    public function frontpageAction($id)
    {
        if (!$node = Node::find($id) or !$type = App::module('system/site')->getType($node->type)) {
            App::abort(404, __('Node not found.'));
        }

        if (isset($type['frontpage']) and !$type['frontpage']) {
            App::abort(400, __('Invalid node type.'));
        }

        App::config('system/site')->set('frontpage', $id);
        return ['message' => 'success'];
    }
}
