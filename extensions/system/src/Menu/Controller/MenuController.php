<?php

namespace Pagekit\Menu\Controller;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\Menu\Entity\ItemRepository;
use Pagekit\Menu\Entity\Menu;

/**
 * @Access("system: manage menus", admin=true)
 */
class MenuController extends Controller
{
    /**
     * @var Repository
     */
    protected $menus;

    /**
     * @var ItemRepository
     */
    protected $items;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->menus  = $this['menus']->getMenuRepository();
        $this->items  = $this['menus']->getItemRepository();
    }

    /**
     * @Request({"id": "int"})
     * @Response("extension://system/views/admin/menu/index.razr")
     */
    public function indexAction($id = null)
    {
        $menus = $this->menus->query()->orderBy('name')->get();

        if ($menu = $id === null && count($menus) ? current($menus) : (isset($menus[$id]) ? $menus[$id] : false)) {
            $menu->setItems($this->items->findByMenu($menu));
        }

        return ['head.title' => __('Menus'), 'menu' => $menu, 'menus' => $menus];
    }

    /**
     * @Request({"id": "int", "name"}, csrf=true)
     */
    public function saveAction($id, $name)
    {
        try {

            if (!$name) {
                throw new Exception(__('Invalid menu name.'));
            }

            if (!$menu = $this->menus->find($id)) {
                $menu = new Menu;
            }

            if ($this->menus->where(['name = ?', 'id <> ?'], [$name, $id])->first()) {
                throw new Exception(__('Invalid menu name. "%name%" is already in use.', ['%name%' => $name]));
            }

            $this->menus->save($menu, compact('name'));

        } catch (Exception $e) {
            $this['message']->error($e->getMessage());
        }

        return $this->redirect('@system/menu', ['id' => isset($menu) ? $menu->getId() : 0]);
    }

    /**
     * @Request({"id": "int"}, csrf=true)
     */
    public function deleteAction($id)
    {
        try {

            if (!$menu = $this->menus->find($id)) {
                throw new Exception(__('Invalid menu id'));
            }

            $this->menus->delete($menu);

            $this['db']->delete('@system_menu_item', ['menu_id' => $id]);

        } catch (Exception $e) {
            $this['message']->error($e->getMessage());
        }

        return $this->redirect('@system/menu');
    }

    /**
     * @Request({"id": "int", "order": "array"}, csrf=true)
     * @Response("json")
     */
    public function reorderAction($id, $order = [])
    {
        $items = $this->items->findByMenu($id);

        foreach ($order as $data) {

            if (!isset($items[$data['id']])) {
                continue;
            }

            $item = $items[$data['id']];
            $item->setParentId($data['parent_id'] != "" ? $data['parent_id'] : "0");
            $item->setDepth($data['depth']);
            $item->setPriority($data['order']);

            $this->items->save($item);
        }

        return ['message' => __('Menu order updated')];
    }
}
