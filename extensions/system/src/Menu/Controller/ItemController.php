<?php

namespace Pagekit\Menu\Controller;

use Pagekit\Application as App;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\Menu\Entity\Item;
use Pagekit\Menu\Entity\Menu;
use Pagekit\User\Entity\Role;
use Symfony\Component\Routing\Exception\InvalidParameterException;

/**
 * @Route("/menu/item")
 * @Access("system: manage menus", admin=true)
 */
class ItemController extends Controller
{
    /**
     * @Request({"menu": "int"})
     * @Response("extensions/system/views/admin/menu/item.edit.razr")
     */
    public function addAction($id)
    {
        try {

            if (!$menu = Menu::find($id)) {
                throw new Exception(__('Invalid menu.'));
            }

            $item = new Item;
            $item->setMenu($menu);

            return ['head.title' => __('Add Menu Item'), 'item' => $item, 'menu' => $menu, 'roles' => Role::findAll()];

        } catch (Exception $e) {
            App::message()->error($e->getMessage());
        }

        return $this->redirect('@system/menu');
    }

    /**
     * @Request({"id": "int"})
     * @Response("extensions/system/views/admin/menu/item.edit.razr")
     */
    public function editAction($id)
    {
        try {

            if (!$item = Item::find($id)) {
                throw new Exception(__('Invalid menu item.'));
            }

            return ['head.title' => __('Edit Menu Item'), 'item' => $item, 'roles' => Role::findAll()];

        } catch (Exception $e) {
            App::message()->error($e->getMessage());
        }

        return $this->redirect('@system/menu');
    }

    /**
     * @Request({"id": "int", "item": "array", "menu": "int"}, csrf=true)
     */
    public function saveAction($id, $data, $menuId = null)
    {
        try {

            if (!$item = Item::find($id)) {

                if (!$menu = Menu::find($menuId)) {
                    throw new Exception(__('Invalid menu.'));
                }

                $item = new Item;
                $item->setMenu($menu);
                $item->setMenuId($menu->getId());
            }

            if (!$data['url']) {
                $data['url'] = $data['link'];
            }

            Item::save($item, $data);

            $id = $item->getId();

            App::message()->success($id ? __('Menu item saved.') : __('Menu item created.'));

        } catch (Exception $e) {
            App::message()->error($e->getMessage());
        } catch (InvalidParameterException $e) {
            App::message()->error(__('Invalid url.'));
        }

        return $id ? $this->redirect('@system/item/edit', compact('id')) : $this->redirect('@system/item/add', ['menu' => $menuId]);
    }

    /**
     * @Request({"menu": "int", "id": "int[]"}, csrf=true)
     */
    public function deleteAction($menuId, $ids = [])
    {
        try {

            if (!$menu = Menu::find($menuId)) {
                throw new Exception(__('Invalid menu.'));
            }

            $items = Item::findByMenu($menu);

            foreach ($ids as $id) {
                if (isset($items[$id])) {
                    Item::delete($items[$id]);
                }
            }

            App::message()->success(_c('{0} No menu item deleted.|{1} Menu item deleted.|]1,Inf[ Menu items deleted.', count($ids)));

        } catch (Exception $e) {
            App::message()->error($e->getMessage());
        }

        return $this->redirect('@system/menu', ['id' => $menuId]);
    }

    /**
     * @Request({"status": "int", "menu": "int", "id": "int[]"}, csrf=true)
     */
    public function statusAction($status, $menuId, $ids = [])
    {
        try {

            if (!$menu = Menu::find($menuId)) {
                throw new Exception(__('Invalid menu.'));
            }

            foreach ($ids as $id) {
                if ($item = Item::find($id) and $item->getStatus() != $status) {
                    Item::save($item, compact('status'));
                }
            }

            if ($status == Item::STATUS_ENABLED) {
                $message = _c('{0} No menu item enabled.|{1} Menu item enabled.|]1,Inf[ Menu items enabled.', count($ids));
            } else {
                $message = _c('{0} No menu item disabled.|{1} Menu item disabled.|]1,Inf[ Menu items disabled.', count($ids));
            }

            App::message()->success($message);
        } catch (Exception $e) {
            App::message()->error($e->getMessage());
        }

        return $this->redirect('@system/menu', ['id' => $menuId]);
    }
}
