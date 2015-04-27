<?php

namespace Pagekit\Dashboard\Controller;

use Pagekit\Application as App;
use Pagekit\Kernel\Exception\NotFoundException;
use Pagekit\Module\Module;
use Pagekit\Widget\Model\Widget;

/**
 * @Access(admin=true)
 * @Route(name="")
 */
class DashboardController
{
    /**
     * @var Module
     */
    protected $dashboard;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->dashboard = App::module('system/dashboard');
    }

    /**
     * @Route("/", methods="GET")
     */
    public function indexAction()
    {
        $widgets = [];
        $columns = [];

        foreach ($this->dashboard->getWidgets() as $id => $data) {

            $widget = Widget::create($data);

            if ($type = $this->dashboard->getType($widget->getType())) {
                $widgets[$id] = $type->render($widget);
                $columns[] = $id;
            }
        }

        $columns = $this->chunkList($columns, 3);

        return [
            '$view' => [
                'title' => __('Dashboard'),
                'name'  => 'system/dashboard:views/admin/index.php'
            ],
            'widgets' => $widgets,
            'columns' => $columns
        ];
    }

    public function settingsAction()
    {
        return [
            '$view' => [
                'title' => __('Dashboard Settings'),
                'name'  => 'system/dashboard:views/admin/settings.php'
            ],
            '$dashboard' => [
                'types' => $this->dashboard->getTypes(),
                'widgets' => $this->dashboard->getWidgets()
            ]
        ];
    }

    /**
     * @Request({"type"})
     */
    public function addAction($id)
    {
        if (!$type = $this->dashboard->getType($id)) {
            throw new NotFoundException(__('Widget type not found.'));
        }

        $widget = Widget::create(['type' => $id]);

        return [
            '$view' => [
                'title' => __('Add Widget'),
                'name'  => 'system/dashboard:views/admin/edit.php'
            ],
            '$data' => [
                'type' => $type,
                'widget' => $widget
            ],
            'type' => $type,
            'widget' => $widget
        ];
    }

    /**
     * @Request({"id"})
     */
    public function editAction($id)
    {
        if (!$widget = $this->dashboard->getWidget($id)) {
            throw new NotFoundException(__('Widget not found.'));
        }

        if (!$type = $this->dashboard->getType($widget['type'])) {
            throw new NotFoundException(__('Widget type not found.'));
        }

        $widget = Widget::create($widget);

        return [
            '$view' => [
                'title' => __('Edit Widget'),
                'name'  => 'system/dashboard:views/admin/edit.php'
            ],
            '$data' => [
                'type' => $type,
                'widget' => $widget
            ],
            'type' => $type,
            'widget' => $widget
        ];
    }

    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST")
     * @Request({"id", "widget": "array"}, csrf=true)
     */
    public function saveAction($id = 0, $widget = [])
    {
        if ($new = !$id) {
            $id = uniqid();
        }

        $widget['id'] = $id;

        $this->dashboard->saveWidgets(array_merge($this->dashboard->getWidgets(), [$id => $widget]));

        return $widget;
    }

    /**
     * @Request({"ids": "array"}, csrf=true)
     */
    public function deleteAction($ids = [])
    {
        $widgets = $this->dashboard->getWidgets();

        foreach ($ids as $id) {
            unset($widgets[$id]);
        }

        $this->dashboard->saveWidgets($widgets);

        return ['message' => _c('{0} No widgets deleted.|{1} Widget deleted.|]1,Inf[ Widgets deleted.', count($ids)), 'widgets' => $widgets];
    }

    /**
     * @Request({"order": "array"}, csrf=true)
     */
    public function reorderAction($order = [])
    {
        $widgets = $this->dashboard->getWidgets();
        $reordered = [];

        foreach ($order as $id) {
            if ($widget = $this->dashboard->getWidget($id)) {
                $reordered[$id] = $widget;
            }
        }

        if (count($widgets) == count($reordered)) {
            $this->dashboard->saveWidgets($reordered);
        }

        return ['message' => __('Widgets reordered.')];
    }

    protected function chunkList($list, $p)
    {
        $listlen   = count($list);
        $partlen   = floor($listlen / $p);
        $partrem   = $listlen % $p;
        $partition = [];
        $mark      = 0;

        for ($px = 0; $px < $p; $px++) {
            $incr = ($px < $partrem) ? $partlen + 1 : $partlen;
            $partition[$px] = array_slice($list, $mark, $incr);
            $mark += $incr;
        }

        return $partition;
    }
}
