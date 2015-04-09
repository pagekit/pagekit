<?php

namespace Pagekit\Widget\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Application\Exception;
use Pagekit\User\Entity\Role;
use Pagekit\Widget\Entity\Widget;
use Pagekit\Widget\Event\RegisterPositionEvent;
use Pagekit\Widget\Event\WidgetCopyEvent;
use Pagekit\Widget\Event\WidgetEditEvent;
use Pagekit\Widget\Event\WidgetEvent;
use Pagekit\Widget\Model\TypesTrait;

/**
 * @Access("system: manage widgets", admin=true)
 */
class WidgetsController extends Controller
{
    /**
     * @var array
     */
    protected $positions;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->positions = App::trigger('system.positions', new RegisterPositionEvent)->getPositions();
    }

    /**
     * @Response("system/widget:views/admin/index.razr")
     */
    public function indexAction()
    {
        $this->positions[''] = ['name' => __('Unassigned Widgets')];

        $widgets = [];

        foreach (Widget::query()->orderBy('priority', 'ASC')->get() as $widget) {
            $position = $widget->getPosition();
            $widgets[isset($this->positions[$position]) ? $position : ''][] = $widget;
        }

        return [
            '$meta' => [
                'title' => __('Widgets')
            ],
            'widgets' => $widgets,
            'positions' => $this->positions,
            'types' => TypesTrait::getWidgetTypes()
        ];
    }

    /**
     * @Request({"type"})
     * @Response("system/widget:views/admin/edit.razr")
     */
    public function addAction($type)
    {
        $widget = new Widget;
        $widget->setType($type);

        return [
            '$meta' => [
                'title' => __('Add Widget')
            ],
            'widget' => $widget,
            'roles' => Role::findAll(),
            'positions' => $this->positions,
            'additionals' => $this->triggerEditEvent($widget)
        ];
    }

    /**
     * @Request({"id": "int"})
     * @Response("system/widget:views/admin/edit.razr")
     */
    public function editAction($id)
    {
        try {

            if (!$widget = Widget::find($id)) {
                throw new Exception(__('Invalid widget id'));
            }

            return [
                '$meta' => [
                    'title' => __('Edit Widget')
                ],
                'widget' => $widget,
                'roles' => Role::findAll(),
                'positions' => $this->positions,
                'additionals' => $this->triggerEditEvent($widget)
            ];

        } catch (Exception $e) {
            App::message()->error($e->getMessage());
        }

        return $this->redirect('@system/widgets');
    }

    /**
     * @Request({"id": "int", "widget": "array"}, csrf=true)
     */
    public function saveAction($id, $data)
    {
        try {

            // is new ?
            if (!$widget = Widget::find($id)) {

                if ($id) {
                    throw new Exception(__('Invalid widget id'));
                }

                $widget = new Widget;
            }

            $data['menuItems'] = array_filter((array) @$data['menuItems']);
            $data['settings']  = array_merge(['show_title' => 0], isset($data['settings']) ? $data['settings'] : []);

            $widget->save($data);

            App::trigger('system.widget.save', new WidgetEvent($widget));

            $id = $widget->getId();

            App::message()->success($id ? __('Widget saved.') : __('Widget created.'));

        } catch (Exception $e) {

            App::message()->error($e->getMessage());
        }

        return $id ? $this->redirect('@system/widgets/edit', compact('id')) : $this->redirect('@system/widgets/add', ['type' => $data['type']]);
    }

    /**
     * @Request({"ids": "int[]"}, csrf=true)
     */
    public function deleteAction($ids = [])
    {
        foreach ($ids as $id) {
            if ($widget = Widget::find($id)) {
                $widget->delete();
            }
        }

        App::message()->success(_c('{0} No widget deleted.|{1} Widget deleted.|]1,Inf[ Widgets deleted.', count($ids)));

        return $this->redirect('@system/widgets');
    }


    /**
     * @Request({"ids": "int[]"}, csrf=true)
     */
    public function copyAction($ids = [])
    {
        foreach ($ids as $id) {

            if (!$widget = Widget::find($id)) {
                continue;
            }

            $copy = clone $widget;
            $copy->setId(null);
            $copy->setStatus(Widget::STATUS_DISABLED);
            $copy->setTitle($widget->getTitle().' - '.__('Copy'));
            $copy->save();

            App::trigger('system.widget.copy', new WidgetCopyEvent($widget, $copy));
        }

        return $this->redirect('@system/widgets');
    }

    /**
     * @Request({"ids": "int[]"}, csrf=true)
     */
    public function enableAction($ids = [])
    {
        foreach ($ids as $id) {
            if ($widget = Widget::find($id) and !$widget->getStatus()) {
                $widget->save(['status' => Widget::STATUS_ENABLED]);
            }
        }

        return $this->redirect('@system/widgets');
    }

    /**
     * @Request({"ids": "int[]"}, csrf=true)
     */
    public function disableAction($ids = [])
    {
        foreach ($ids as $id) {
            if ($widget = Widget::find($id) and $widget->getStatus()) {
                $widget->save(['status' => Widget::STATUS_DISABLED]);
            }
        }

        return $this->redirect('@system/widgets');
    }

    /**
     * @Request({"position", "order": "array"}, csrf=true)
     * @Response("json")
     */
    public function reorderAction($position, $order = [])
    {
        $widgets = Widget::findAll();

        foreach ($order as $priority => $data) {

            $id = $data['id'];

            if (isset($widgets[$id])) {
                $widgets[$id]->save(compact('position', 'priority'));
            }
        }

        return ['message' => __('Widgets updated.')];
    }

    protected function triggerEditEvent($widget)
    {
        return App::trigger('system.widget.edit', new WidgetEditEvent($widget))->getSettings();
    }
}
