<?php

namespace Pagekit\Site\Controller;

use Pagekit\Application as App;
use Pagekit\Site\Model\Node;

/**
 * @Access("site: manage site")
 */
class NodeController
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
        return Node::find($id);
    }

    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
     * @Request({"node": "array", "id": "int"}, csrf=true)
     */
    public function saveAction($data, $id = 0)
    {
        if (!$node = Node::find($id)) {
            $node = new Node;
            unset($data['id']);
        }

        if (!$data['slug'] = $this->slugify($data['slug'] ?: $data['title'])) {
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

            if ($type = App::module('system/site')->getType($node->getType()) and isset($type['protected']) and $type['protected']) {
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

                $node->setParentId($data['parent_id']);
                $node->setPriority($data['order']);
                $node->setMenu($menu);

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
        App::config('system/site')->set('frontpage', $id);
        return ['message' => 'success'];
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
