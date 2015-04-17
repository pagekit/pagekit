<?php

namespace Pagekit\Widget\Controller;

use Pagekit\Application as App;
use Pagekit\Widget\Entity\Widget;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Access("system: manage widgets")
 * @Route("", name="")
 * @Response("json")
 */
class WidgetsApiController
{
    /**
     * @Route("/", methods="GET")
     * @Request({"search"})
     */
    public function indexAction($search = '')
    {
        return array_values($search ? Widget::where('title LIKE :search', ['search' => "%{$search}%"])->get() : Widget::findAll());
    }

    /**
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     */
    public function getAction($id)
    {
        if (!$widget = Widget::find($id)) {
            throw new NotFoundHttpException('Widget not found.');
        }

        return $widget;
    }


    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
     * @Request({"widget": "array", "id": "int"}, csrf=true)
     */
    public function saveAction($data, $id = 0)
    {
        if ($id) {

            if (!$widget = Widget::find($id)) {
                throw new NotFoundHttpException('Widget not found.');
            }

        } else {

            $widget = new Widget;

        }

        $widget->save($data);

        return $widget;
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
     * @Request({"id": "int"}, csrf=true)
     */
    public function deleteAction($id)
    {
        if ($widget = Widget::find($id)) {
            $widget->delete();
        } else {
            throw new NotFoundHttpException('Widget not found.');
        }

        return 'success';
    }

    /**
     * @Route("/bulk", methods="POST")
     * @Request({"widgets": "array"}, csrf=true)
     */
    public function bulkSaveAction($widgets = [])
    {
        foreach ($widgets as $data) {
            $this->saveAction($data, null, null, isset($data['id']) ? $data['id'] : 0);
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
     * @Request({"position", "widgets": "array"}, csrf=true)
     */
    public function updateOrderAction($position, $widgets = []) {
        foreach ($widgets as $data) {
            if ($widget = Widget::find($data['id'])) {

                $widget->setPriority($data['order']);
                $widget->setPosition($position);

                $widget->save();
            }
        }

        return 'success';
    }
}
