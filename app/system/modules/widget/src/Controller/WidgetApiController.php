<?php

namespace Pagekit\Widget\Controller;

use Pagekit\Application as App;
use Pagekit\Kernel\Exception\NotFoundException;
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
            throw new NotFoundException('Widget not found.');
        }

        return $widget;
    }

    /**
     * @Request({"position", "ids": "array"}, csrf=true)
     */
    public function assignAction($position, $ids)
    {
        $theme = App::theme();
        $theme->assignPosition($position, $ids);

        return ['message' => 'success', 'theme' => $theme];
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
            throw new NotFoundException('Widget not found.');
        }

        if (isset($data['position'])) {
            $widget->position = $data['position'];
        }

        $widget->save($data);

        return ['message' => __('Widget saved.'), 'widget' => Widget::find($widget->id)];
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
     * @Request({"id": "int"}, csrf=true)
     */
    public function deleteAction($id)
    {
        if (!$widget = Widget::find($id)) {
            throw new NotFoundException('Widget not found.');
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
}
