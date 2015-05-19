<?php

namespace Pagekit\Widget\Controller;

use Pagekit\Application as App;
use Pagekit\Kernel\Exception\NotFoundException;
use Pagekit\Widget\Entity\Widget;

/**
 * @Access("system: manage widgets")
 */
class WidgetsApiController
{
    protected $widgets;

    public function __construct()
    {
        $this->widgets = App::module('system/widget');
    }

    /**
     * @Route("/", methods="GET")
     * @Request({"grouped": "bool"})
     */
    public function indexAction($grouped = false)
    {
        $widgets = Widget::findAll();

        if (!$grouped) {
            return $widgets;
        }

        $positions = ['' => []];

        foreach ($this->widgets->config('widget.positions') as $position => $assigned) {

            if (!$this->widgets->hasPosition($position)) {
                $position = '';
            }

            foreach ($assigned as $id) {
                if (isset($widgets[$id])) {
                    $positions[$position][] = $widgets[$id];
                    unset($widgets[$id]);
                }
            }
        }

        $positions[''] = array_merge($positions[''], array_values($widgets));

        return $positions;
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
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
     * @Request({"widget": "array", "id": "int", "position", "config": "array"}, csrf=true)
     */
    public function saveAction($data, $id = 0, $position = false, $config = false)
    {
        if ($id) {

            if (!$widget = Widget::find($id)) {
                throw new NotFoundException('Widget not found.');
            }

        } else {

            $widget = new Widget;

        }

        $widget->save($data);

        if (false !== $config) {
            App::config('system/widget')->set('widget.config.' . $widget->getId(), $config);
        }

        $positions = $this->widgets->config('widget.positions');

        if ($position && !isset($positions[$position]) || !in_array($widget->getId(), $positions[$position])) {
            $positions = $this->filterPositions($positions, [$widget->getId()]);
            $positions[$position][] = $widget->getId();
            App::config('system/widget')->set('widget.positions', $positions);
        }

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
            throw new NotFoundException('Widget not found.');
        }

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

    /**
     * @Route("/config", methods="GET")
     */
    public function configAction()
    {
        return array_merge($this->widgets->config('widget.config', new \stdClass()), ['defaults' => $this->widgets->config('widget.defaults')]);
    }

    /**
     * @Route("/positions", methods="POST")
     * @Request({"position", "widgets": "array"}, csrf=true)
     */
    public function positionsAction($position, $widgets = [])
    {
        $positions = $this->widgets->config('widget.positions');
        $positions = $this->filterPositions($positions, $widgets);

        $positions[$position] = $widgets;

        App::config('system/widget')->set('widget.positions', $positions);

        return ['message' => 'success'];
    }

    /**
     * @param  array $positions
     * @param  array $widgets
     * @return mixed
     */
    protected function filterPositions(array $positions = [], $widgets = [])
    {
        foreach ($positions as $pos => $ids) {
            $positions[$pos] = array_diff($ids, $widgets);
        }
        return $positions;
    }
}
