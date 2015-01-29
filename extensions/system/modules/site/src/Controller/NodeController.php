<?php

namespace Pagekit\Site\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Application\Exception;
use Pagekit\Site\Entity\Node;

/**
 * @Route("/site/node")
 * @Access("system: manage site", admin=true)
 */
class NodeController extends Controller
{
    /**
     * @Response("json")
     */
    public function indexAction()
    {
        return Node::findAll();
    }

    /**
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     * @Response("json")
     */
    public function getAction($id)
    {
        return Node::find($id);
    }

    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
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
     * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
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
     * @Route("/bulk", methods="POST")
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
     * @Route("/bulk", methods="DELETE")
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
