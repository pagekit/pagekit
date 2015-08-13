<?php

namespace Pagekit\Widget\Controller;

use Pagekit\Application as App;
use Pagekit\Widget\Model\Widget;

/**
 * @Access("system: manage widgets")
 */
class WidgetApiController
{
    /**
     * @Route("/", methods="GET")
     */
    public function indexAction()
    {
        return array_values(Widget::findAll());
    }

    /**
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     */
    public function getAction($id)
    {
        if (!$widget = Widget::find($id)) {
            App::abort(404, 'Widget not found.');
        }

        return $widget;
    }

    /**
     * @Request({"position", "ids": "array"}, csrf=true)
     */
    public function assignAction($position, $ids)
    {
        App::position()->assign($position, $ids);

        return ['message' => 'success', 'positions' => array_values(App::position()->all())];
    }

    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
     * @Request({"widget": "array", "id": "int"}, csrf=true)
     */
    public function saveAction($data, $id = 0)
    {
        if (!$id) {
            $widget = Widget::create();
        } else if (!$widget = Widget::find($id)) {
            App::abort(404, 'Widget not found.');
        }

        $widget->save($data);

        return ['message' => 'success', 'widget' => $widget];
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
     * @Request({"id": "int"}, csrf=true)
     */
    public function deleteAction($id)
    {
        if (!$widget = Widget::find($id)) {
            App::abort(404, 'Widget not found.');
        }

        $widget->delete();

        return ['message' => 'success'];
    }

    /**
     * @Route("/bulk", methods="POST")
     * @Request({"widgets": "array"}, csrf=true)
     */
    public function bulkSaveAction($widgets = [])
    {
        foreach ($widgets as $data) {
            $this->saveAction($data, isset($data['id']) ? $data['id'] : 0);
        }

        return ['message' => 'success', 'positions' => array_values(App::position()->all())];
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
}
