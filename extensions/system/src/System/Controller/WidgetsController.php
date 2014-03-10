<?php

namespace Pagekit\System\Controller;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\System\Entity\Widget;

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
    protected $levels;

    /**
     * @var array
     */
    protected $positions;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->widgets = $this('widgets')->getWidgetRepository();
        $this->levels  = $this('users')->getAccessLevelRepository();

        if (isset(self::$app['theme.site'])) {
            foreach ($this('theme.site')->getConfig('positions', array()) as $id => $position) {
                list($name, $description) = array_merge((array) $position, array(''));
                $this->positions[$id] = compact('id', 'name', 'description');
            }
        }
    }

    /**
     * @View("system/admin/widgets/index.razr.php")
     */
    public function indexAction()
    {
        $widgets        = array();
        $notusedwidgets = array();

        foreach ($this->widgets->query()->orderBy('priority', 'ASC')->get() as $widget) {

            $position = $widget->getPosition();

            if (!isset($this->positions[$position])) {
                $notusedwidgets[] = $widget;
                continue;
            }

            $widgets[$position][] = $widget;
        }

        return array('head.title' => __('Widgets'), 'widgets' => $widgets, 'levels' => $this->levels->findAll(), 'positions' => $this->positions, 'notusedwidgets' => $notusedwidgets);
    }

    /**
     * @Request({"type"})
     * @View("system/admin/widgets/edit.razr.php")
     */
    public function addAction($type)
    {
        $widget = new Widget;
        $widget->setType($type);

        return array('head.title' => __('Add Widget'), 'widget' => $widget, 'levels' => $this->levels->findAll(), 'positions' => $this->positions);
    }

    /**
     * @Request({"id": "int"})
     * @View("system/admin/widgets/edit.razr.php")
     */
    public function editAction($id)
    {
        $widget = $this->widgets->find($id);

        return array('head.title' => __('Edit Widget'), 'widget' => $widget, 'levels' => $this->levels->findAll(), 'positions' => $this->positions);
    }

    /**
     * @Request({"id": "int", "widget": "array"})
     */
    public function saveAction($id, $data)
    {
        try {

            // is new ?
            if (!$widget = $this->widgets->find($id)) {

                if ($id) {
                    throw new Exception(__('No widget found for id "%id%"', array('%id%' => $id)));
                }

                $widget = new Widget;
            }

            $data['settings']['show_title'] = isset($data['settings']['show_title']);
            $data['menuItems'] = array_filter((array) @$data['menuItems']);

            $this->widgets->save($widget, $data);
            $id = $widget->getId();

            $this('message')->success($id ? __('Widget saved.') : __('Widget created.'));

        } catch (Exception $e) {

            $this('message')->error($e->getMessage());

        }

        return $id ? $this->redirect('@system/widgets/edit', compact('id')) : $this->redirect('@system/widgets/add', array('type' => $data['type']));
    }

    /**
     * @Request({"ids": "int[]"})
     */
    public function deleteAction($ids = array())
    {
        foreach ($ids as $id) {
            if ($widget = $this->widgets->find($id)) {
                $this->widgets->delete($widget);
            }
        }

        $this('message')->success(_c('{0} No widget deleted.|{1} Widget deleted.|]1,Inf[ Widgets deleted.', count($ids)));

        return $this->redirect('@system/widgets/index');
    }


    /**
     * @Request({"ids": "int[]"})
     */
    public function copyAction($ids = array())
    {
        foreach ($ids as $id) {
            if ($widget = $this->widgets->find($id)) {

                $widget = clone $widget;
                $widget->setId(null);
                $widget->setStatus(Widget::STATUS_DISABLED);
                $widget->setTitle($widget->getTitle().' - '.__('Copy'));

                $this->widgets->save($widget);
            }
        }

        return $this->redirect('@system/widgets/index');
    }


    /**
     * @Request({"ids": "int[]"})
     */
    public function enableAction($ids = array())
    {
        foreach ($ids as $id) {
            if ($widget = $this->widgets->find($id) and !$widget->getStatus()) {
                $this->widgets->save($widget, array('status' => Widget::STATUS_ENABLED));
            }
        }

        return $this->redirect('@system/widgets/index');
    }

    /**
     * @Request({"ids": "int[]"})
     */
    public function disableAction($ids = array())
    {
        foreach ($ids as $id) {
            if ($widget = $this->widgets->find($id) and $widget->getStatus()) {
                $this->widgets->save($widget, array('status' => Widget::STATUS_DISABLED));
            }
        }

        return $this->redirect('@system/widgets/index');
    }

    /**
     * @Request({"position", "order": "array"})
     */
    public function reorderAction($position, $order = array())
    {
        $widgets = $this->widgets->findAll();

        foreach ($order as $priority => $data) {

            $id = $data['id'];

            if (isset($widgets[$id])) {
                $this->widgets->save($widgets[$id], compact('position', 'priority'));
            }
        }

        return $this('response')->json(array('message' => __('Widgets updated.')));
    }
}
