<?php

namespace Pagekit\Menu\Controller;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\Menu\Entity\Item;
use Pagekit\Menu\Entity\ItemRepository;
use Symfony\Component\Routing\Exception\InvalidParameterException;

/**
 * @Route("/system/menu/item")
 * @Access("system: manage menus", admin=true)
 */
class ItemController extends Controller
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
     * @var Repository
     */
    protected $roles;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->menus = $this['menus']->getMenuRepository();
        $this->items = $this['menus']->getItemRepository();
        $this->roles = $this['users']->getRoleRepository();
    }

    /**
     * @Request({"menu": "int"})
     * @Response("extension://system/views/admin/menu/item.edit.razr")
     */
    public function addAction($id)
    {
        try {

            if (!$menu = $this->menus->find($id)) {
                throw new Exception(__('Invalid menu.'));
            }

            $item = new Item;
            $item->setMenu($menu);

            return ['head.title' => __('Add Menu Item'), 'item' => $item, 'menu' => $menu, 'roles' => $this->roles->findAll()];

        } catch (Exception $e) {
            $this['message']->error($e->getMessage());
        }

        return $this->redirect('@system/menu');
    }

    /**
     * @Request({"id": "int"})
     * @Response("extension://system/views/admin/menu/item.edit.razr")
     */
    public function editAction($id)
    {
        try {

            if (!$item = $this->items->find($id)) {
                throw new Exception(__('Invalid menu item.'));
            }

            return ['head.title' => __('Edit Menu Item'), 'item' => $item, 'roles' => $this->roles->findAll()];

        } catch (Exception $e) {
            $this['message']->error($e->getMessage());
        }

        return $this->redirect('@system/menu');
    }

    /**
     * @Request({"id": "int", "item": "array", "menu": "int"}, csrf=true)
     */
    public function saveAction($id, $data, $menuId = null)
    {
        try {

            if (!$item = $this->items->find($id)) {

                if (!$menu = $this->menus->find($menuId)) {
                    throw new Exception(__('Invalid menu.'));
                }

                $item = new Item;
                $item->setMenu($menu);
                $item->setMenuId($menu->getId());
            }

            if (!$data['url']) {
                $data['url'] = $data['link'];
            }

            $this->items->save($item, $data);

            $id = $item->getId();

            $this['message']->success($id ? __('Menu item saved.') : __('Menu item created.'));

        } catch (Exception $e) {
            $this['message']->error($e->getMessage());
        } catch (InvalidParameterException $e) {
            $this['message']->error(__('Invalid url.'));
        }

        return $id ? $this->redirect('@system/item/edit', compact('id')) : $this->redirect('@system/item/add', ['menu' => $menuId]);
    }

    /**
     * @Request({"menu": "int", "id": "int[]"}, csrf=true)
     */
    public function deleteAction($menuId, $ids = [])
    {
        try {

            if (!$menu = $this->menus->find($menuId)) {
                throw new Exception(__('Invalid menu.'));
            }

            $items = $this->items->findByMenu($menu);

            foreach ($ids as $id) {
                if (isset($items[$id])) {
                    $this->items->delete($items[$id]);
                }
            }

            $this['message']->success(_c('{0} No menu item deleted.|{1} Menu item deleted.|]1,Inf[ Menu items deleted.', count($ids)));

        } catch (Exception $e) {
            $this['message']->error($e->getMessage());
        }

        return $this->redirect('@system/menu', ['id' => $menuId]);
    }

    /**
     * @Request({"status": "int", "menu": "int", "id": "int[]"}, csrf=true)
     */
    public function statusAction($status, $menuId, $ids = [])
    {
        try {

            if (!$menu = $this->menus->find($menuId)) {
                throw new Exception(__('Invalid menu.'));
            }

            foreach ($ids as $id) {
                if ($item = $this->items->find($id) and $item->getStatus() != $status) {
                    $this->items->save($item, compact('status'));
                }
            }

            if ($status == Item::STATUS_ENABLED) {
                $message = _c('{0} No menu item enabled.|{1} Menu item enabled.|]1,Inf[ Menu items enabled.', count($ids));
            } else {
                $message = _c('{0} No menu item disabled.|{1} Menu item disabled.|]1,Inf[ Menu items disabled.', count($ids));
            }

            $this['message']->success($message);
        } catch (Exception $e) {
            $this['message']->error($e->getMessage());
        }

        return $this->redirect('@system/menu', ['id' => $menuId]);
    }
}
