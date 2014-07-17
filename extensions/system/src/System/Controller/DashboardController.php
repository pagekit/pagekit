<?php

namespace Pagekit\System\Controller;

use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\User\Model\UserInterface;
use Pagekit\Widget\Event\RegisterWidgetEvent;
use Pagekit\Widget\Model\Widget;

/**
 * @Access(admin=true)
 */
class DashboardController extends Controller
{
    /**
     * @var RegisterWidgetEvent
     */
    protected $types;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->types = $this['events']->dispatch('system.dashboard', new RegisterWidgetEvent);
    }

    /**
     * @Response("extension://system/views/admin/dashboard/index.razr")
     */
    public function indexAction()
    {
        $widgets = [];
        $columns = [];

        foreach ($this->getWidgets() as $id => $data) {
            if ($type = $this->types[$data['type']]) {
                $widgets[$id] = $type->render($this->create($id, $data));
                $columns[] = $id;
            }
        }

        $columns = $this->chunkList($columns, 3);

        return ['head.title' => __('Dashboard'), 'theme.boxed' => false, 'widgets' => $widgets, 'columns' => $columns];
    }

    /**
     * @Response("extension://system/views/admin/dashboard/settings.razr")
     */
    public function settingsAction()
    {
        $widgets = [];

        foreach ($this->getWidgets() as $id => $data) {
            if ($type = $this->types[$data['type']]) {

                $widget = $this->create($id, $data);
                $widget->setType($type->getName());
                $widget->setTitle($type->getDescription($widget));

                $widgets[$id] = $widget;
            }
        }

        return ['head.title' => __('Dashboard Settings'), 'types' => $this->types, 'widgets' => $widgets];
    }

    /**
     * @Request({"type"})
     * @Response("extension://system/views/admin/dashboard/edit.razr")
     */
    public function addAction($id)
    {
        try {

            if (!$type = $this->types[$id]) {
                throw new Exception(__('Invalid widget type.'));
            }

            $widget = new Widget;
            $widget->setType($id);

            return ['head.title' => __('Add Widget'), 'type' => $type, 'widget' => $widget];

        } catch (Exception $e) {
            $this['message']->error($e->getMessage());
        }

        return $this->redirect('@system/dashboard/settings');
    }

    /**
     * @Request({"id"})
     * @Response("extension://system/views/admin/dashboard/edit.razr")
     */
    public function editAction($id)
    {
        try {

            $widgets = $this->getWidgets();

            if (!isset($widgets[$id]) or !isset($widgets[$id]['type'])) {
                throw new Exception(__('Invalid widget id.'));
            }

            if (!$type = $this->types[$widgets[$id]['type']]) {
                throw new Exception(__('Invalid widget type.'));
            }

            $widget = $this->create($id, $widgets[$id]);

            return ['head.title' => __('Edit Widget'), 'type' => $type, 'widget' => $widget];

        } catch (Exception $e) {
            $this['message']->error($e->getMessage());
        }

        return $this->redirect('@system/dashboard/settings');
    }

    /**
     * @Request({"id", "widget": "array"}, csrf=true)
     */
    public function saveAction($id = 0, $widget = [])
    {
        try {

            if ($new = !$id) {
                $id = uniqid();
            }

            $widgets      = $this->getWidgets();
            $widgets[$id] = $widget;

            $this->save($widgets);

            $this['message']->success($new ? __('Widget created.') : __('Widget saved.'));

        } catch (Exception $e) {
            $this['message']->error($e->getMessage());
        }
        return $this->redirect($id ? '@system/dashboard/edit' : '@system/dashboard/add', compact('id'));
    }

    /**
     * @Request({"ids": "array"}, csrf=true)
     */
    public function deleteAction($ids = [])
    {
        $widgets = $this->getWidgets();

        foreach ($ids as $id) {
            unset($widgets[$id]);
        }

        $this->save($widgets);

        $this['message']->success(_c('{0} No widgets deleted.|{1} Widget deleted.|]1,Inf[ Widgets deleted.', count($ids)));

        return $this->redirect('@system/dashboard/settings');
    }

    /**
     * @Request({"order": "array"}, csrf=true)
     * @Response("json")
     */
    public function reorderAction($order = [])
    {
        $reordered = [];
        $widgets = $this->getWidgets();

        foreach ($order as $data) {
            $id = $data['id'];
            if (isset($widgets[$id])) {
                $reordered[$id] = $widgets[$id];
            }
        }

        $this->save($reordered);

        return ['message' => __('Widgets reordered.')];
    }

    /**
     * @param string $id
     * @param array  $data
     * @return Widget
     */
    protected function create($id, $data)
    {
        $widget = new Widget;
        $widget->setId($id);
        $widget->setType($data['type']);
        $widget->setSettings($data);

        return $widget;
    }

    /**
     * @param string[]      $dashboard
     * @param UserInterface $user
     */
    protected function save($dashboard, $user = null)
    {
        if (null === $user) {
            $user = $this['user'];
        }

        $users = $this['users']->getUserRepository();

        // make sure user is registered in the entity manager
        $user = $users->find($user->getId());
        $user->set('dashboard', $dashboard);

        $users->save($user);
    }

    /**
     * @return array
     */
    protected function getWidgets()
    {
        return $this['user']->get('dashboard', $this['system']->getConfig('dashboard.default'));
    }

    protected function chunkList($list, $p) {

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
