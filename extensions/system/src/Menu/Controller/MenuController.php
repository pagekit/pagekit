<?php

namespace Pagekit\Menu\Controller;

use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\Menu\Entity\Menu;

/**
 * @Access("system: manage menus", admin=true)
 */
class MenuController extends Controller
{
    /**
     * @Request({"id": "int"})
     * @Response("extensions/system/views/admin/menu/index.razr")
     */
    public function indexAction($id = null)
    {
        $menus = Menu::query()->orderBy('name')->get();

        if ($menu = $id === null && count($menus) ? current($menus) : (isset($menus[$id]) ? $menus[$id] : false)) {
            $menu->setItems(Item::findByMenu($menu));
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

            if (!$menu = Menu::find($id)) {
                $menu = new Menu;
            }

            if (Menu::where(['name = ?', 'id <> ?'], [$name, $id])->first()) {
                throw new Exception(__('Invalid menu name. "%name%" is already in use.', ['%name%' => $name]));
            }

            Menu::save($menu, compact('name'));

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

            if (!$menu = Menu::find($id)) {
                throw new Exception(__('Invalid menu id'));
            }

            Menu::delete($menu);

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
        $items = Item::findByMenu($id);

        foreach ($order as $data) {

            if (!isset($items[$data['id']])) {
                continue;
            }

            $item = $items[$data['id']];
            $item->setParentId($data['parent_id'] != "" ? $data['parent_id'] : "0");
            $item->setDepth($data['depth']);
            $item->setPriority($data['order']);

            Item::save($item);
        }

        return ['message' => __('Menu order updated')];
    }
}
