<?php

namespace Pagekit\Widget\Controller;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
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
     * @var Repository
     */
    protected $widgets;

    /**
     * @var Repository
     */
    protected $roles;

    /**
     * @var array
     */
    protected $positions;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->widgets   = $this['db.em']->getRepository('Pagekit\Widget\Entity\Widget');
        $this->roles     = $this['users']->getRoleRepository();
        $this->positions = $this['events']->dispatch('system.positions', new RegisterPositionEvent)->getPositions();
    }

    /**
     * @Response("extension://system/views/admin/widgets/index.razr")
     */
    public function indexAction()
    {
        $this->positions[''] = ['name' => __('Unassigned Widgets')];

        $widgets = [];

        foreach ($this->widgets->query()->orderBy('priority', 'ASC')->get() as $widget) {
            $position = $widget->getPosition();
            $widgets[isset($this->positions[$position]) ? $position : ''][] = $widget;
        }

        return ['head.title' => __('Widgets'), 'widgets' => $widgets, 'positions' => $this->positions, 'types' => TypesTrait::getWidgetTypes()];
    }

    /**
     * @Request({"type"})
     * @Response("extension://system/views/admin/widgets/edit.razr")
     */
    public function addAction($type)
    {
        $widget = new Widget;
        $widget->setType($type);

        return ['head.title' => __('Add Widget'), 'widget' => $widget, 'roles' => $this->roles->findAll(), 'positions' => $this->positions, 'additionals' => $this->triggerEditEvent($widget)];
    }

    /**
     * @Request({"id": "int"})
     * @Response("extension://system/views/admin/widgets/edit.razr")
     */
    public function editAction($id)
    {
        try {

            if (!$widget = $this->widgets->find($id)) {
                throw new Exception(__('Invalid widget id'));
            }

            return ['head.title' => __('Edit Widget'), 'widget' => $widget, 'roles' => $this->roles->findAll(), 'positions' => $this->positions, 'additionals' => $this->triggerEditEvent($widget)];

        } catch (Exception $e) {
            $this['message']->error($e->getMessage());
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
            if (!$widget = $this->widgets->find($id)) {

                if ($id) {
                    throw new Exception(__('Invalid widget id'));
                }

                $widget = new Widget;
            }

            $data['menuItems'] = array_filter((array) @$data['menuItems']);
            $data['settings']  = array_merge(['show_title' => 0], isset($data['settings']) ? $data['settings'] : []);

            $this->widgets->save($widget, $data);

            $this['events']->dispatch('system.widget.save', new WidgetEvent($widget));

            $id = $widget->getId();

            $this['message']->success($id ? __('Widget saved.') : __('Widget created.'));

        } catch (Exception $e) {

            $this['message']->error($e->getMessage());
        }

        return $id ? $this->redirect('@system/widgets/edit', compact('id')) : $this->redirect('@system/widgets/add', ['type' => $data['type']]);
    }

    /**
     * @Request({"ids": "int[]"}, csrf=true)
     */
    public function deleteAction($ids = [])
    {
        foreach ($ids as $id) {
            if ($widget = $this->widgets->find($id)) {
                $this->widgets->delete($widget);
            }
        }

        $this['message']->success(_c('{0} No widget deleted.|{1} Widget deleted.|]1,Inf[ Widgets deleted.', count($ids)));

        return $this->redirect('@system/widgets');
    }


    /**
     * @Request({"ids": "int[]"}, csrf=true)
     */
    public function copyAction($ids = [])
    {
        foreach ($ids as $id) {

            if (!$widget = $this->widgets->find($id)) {
                continue;
            }

            $copy = clone $widget;
            $copy->setId(null);
            $copy->setStatus(Widget::STATUS_DISABLED);
            $copy->setTitle($widget->getTitle().' - '.__('Copy'));

            $this->widgets->save($copy);

            $this['events']->dispatch('system.widget.copy', new WidgetCopyEvent($widget, $copy));
        }

        return $this->redirect('@system/widgets');
    }

    /**
     * @Request({"ids": "int[]"}, csrf=true)
     */
    public function enableAction($ids = [])
    {
        foreach ($ids as $id) {
            if ($widget = $this->widgets->find($id) and !$widget->getStatus()) {
                $this->widgets->save($widget, ['status' => Widget::STATUS_ENABLED]);
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
            if ($widget = $this->widgets->find($id) and $widget->getStatus()) {
                $this->widgets->save($widget, ['status' => Widget::STATUS_DISABLED]);
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
        $widgets = $this->widgets->findAll();

        foreach ($order as $priority => $data) {

            $id = $data['id'];

            if (isset($widgets[$id])) {
                $this->widgets->save($widgets[$id], compact('position', 'priority'));
            }
        }

        return ['message' => __('Widgets updated.')];
    }

    protected function triggerEditEvent($widget)
    {
        $event = $this['events']->dispatch('system.widget.edit', new WidgetEditEvent($widget));

        return $event->getSettings();
    }
}
