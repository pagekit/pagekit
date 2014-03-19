<?php

namespace Pagekit\System\Controller;

use Pagekit\Widget\Model\TypeManager;
use Pagekit\Widget\Model\Widget;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\System\Event\DashboardInitEvent;
use Pagekit\User\Model\UserInterface;

/**
 * @Access(admin=true)
 */
class DashboardController extends Controller
{
    /**
     * @var TypeManager
     */
    protected $types;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->types = new TypeManager;

        $this('events')->trigger('system.dashboard.init', new DashboardInitEvent($this->types));
    }

    /**
     * @View("system/admin/dashboard/index.razr.php")
     */
    public function indexAction()
    {
        $widgets = array();
        $columns = array();

        foreach ($this->getWidgets() as $id => $data) {
            if ($type = $this->types->get($data['type'])) {
                $widgets[$id] = $type->render($this->create($id, $data));
                $columns[] = $id;
            }
        }

        $columns = $this->chunkList($columns, 3);

        return array('head.title' => __('Dashboard'), 'theme.boxed' => false, 'widgets' => $widgets, 'columns' => $columns);
    }

    /**
     * @View("system/admin/dashboard/settings.razr.php")
     */
    public function settingsAction()
    {
        $widgets = array();

        foreach ($this->getWidgets() as $id => $data) {
            if ($type = $this->types->get($data['type'])) {

                $widget = $this->create($id, $data);
                $widget->setType($type->getName());
                $widget->setTitle($type->getInfo($widget));

                $widgets[$id] = $widget;
            }
        }

        return array('head.title' => __('Dashboard Settings'), 'types' => $this->types, 'widgets' => $widgets);
    }

    /**
     * @Request({"type"})
     * @View("system/admin/dashboard/edit.razr.php")
     */
    public function addAction($id)
    {
        try {

            if (!$type = $this->types->get($id)) {
                throw new Exception(__('Invalid widget type.'));
            }

            $widget = new Widget;
            $widget->setType($id);

            return array('head.title' => __('Add Widget'), 'type' => $type, 'widget' => $widget);

        } catch (Exception $e) {
            $this('message')->error($e->getMessage());
        }

        return $this->redirect('@system/dashboard/settings');
    }

    /**
     * @Request({"id"})
     * @View("system/admin/dashboard/edit.razr.php")
     */
    public function editAction($id)
    {
        try {

            $widgets = $this->getWidgets();

            if (!isset($widgets[$id]) or !isset($widgets[$id]['type'])) {
                throw new Exception(__('Invalid widget id.'));
            }

            if (!$type = $this->types->get($widgets[$id]['type'])) {
                throw new Exception(__('Invalid widget type.'));
            }

            $widget = $this->create($id, $widgets[$id]);

            return array('head.title' => __('Edit Widget'), 'type' => $type, 'widget' => $widget);

        } catch (Exception $e) {
            $this('message')->error($e->getMessage());
        }

        return $this->redirect('@system/dashboard/settings');
    }

    /**
     * @Request({"id", "widget": "array"})
     * @Token
     */
    public function saveAction($id = 0, $widget = array())
    {
        try {

            if (!$id) {
                $id = uniqid();
            }

            $widgets      = $this->getWidgets();
            $widgets[$id] = $widget;

            $this->save($widgets);

            $this('message')->success($id ? __('Widget saved.') : __('Widget created.'));

        } catch (Exception $e) {
            $this('message')->error($e->getMessage());
        }
        return $this->redirect($id ? '@system/dashboard/edit' : '@system/dashboard/add', compact('id'));
    }

    /**
     * @Request({"ids": "array"})
     * @Token
     */
    public function deleteAction($ids = array())
    {
        $widgets = $this->getWidgets();

        foreach ($ids as $id) {
            unset($widgets[$id]);
        }

        $this->save($widgets);

        return $this->redirect('@system/dashboard/settings');
    }

    /**
     * @Request({"order": "array"})
     * @Token
     */
    public function reorderAction($order = array())
    {
        $widgets = $this->getWidgets();

        $reordered = array();

        foreach ($order as $data) {
            $id = $data['id'];
            if (isset($widgets[$id])) {
                $reordered[$id] = $widgets[$id];
            }
        }

        $this->save($reordered);

        return $this('response')->json(array('message' => __('Widgets reordered.')));
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
            $user = $this('user');
        }

        $users = $this('users')->getUserRepository();

        // make sure user is registered in the entity manager
        $user =  $users->find($user->getId());
        $user->set('dashboard', $dashboard);

        $users->save($user);
    }

    /**
     * @return array
     */
    protected function getWidgets()
    {
        return $this('user')->get('dashboard', $this('system')->getConfig('dashboard.default'));
    }

    protected function chunkList($list, $p) {

        $listlen = count($list);
        $partlen = floor($listlen / $p);
        $partrem = $listlen % $p;
        $partition = array();
        $mark = 0;

        for ($px = 0; $px < $p; $px++) {
            $incr = ($px < $partrem) ? $partlen + 1 : $partlen;
            $partition[$px] = array_slice( $list, $mark, $incr );
            $mark += $incr;
        }

        return $partition;
    }
}
