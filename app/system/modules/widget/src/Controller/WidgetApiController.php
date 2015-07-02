<?php

namespace Pagekit\Widget\Controller;

use Pagekit\Application as App;
use Pagekit\Kernel\Exception\NotFoundException;
use Pagekit\Widget\Entity\Widget;

/**
 * @Access("system: manage widgets")
 */
class WidgetApiController
{
    protected $widgets;

    public function __construct()
    {
        $this->widgets = App::module('system/widget');
    }

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
     * @Route("/config", methods="GET")
     */
    public function configAction()
    {
        return $this->widgets->config('widget.config', []) + ['defaults' => $this->widgets->config('widget.defaults')];
    }

    /**
     * @Request({"position", "ids"}, csrf=true)
     */
    public function assignAction($position, $ids)
    {
        $positions = App::positions();

        App::config('system/widget')->set('widget.positions.'.$position, $positions->assign($position, $ids));

        return ['message' => 'success', 'positions' => $positions];
    }

    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
     * @Request({"widget": "array", "id": "int"}, csrf=true)
     */
    public function saveAction($data, $id = 0)
    {
        if (!$id) {
            $widget = new Widget();
        } else if (!$widget = Widget::find($id)) {
            throw new NotFoundException('Widget not found.');
        }

        $widget->position = $data['position'];
        $widget->save($data);

        return $widget;
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
