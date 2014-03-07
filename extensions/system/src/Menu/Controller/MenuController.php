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
     * @var Repository
     */
    protected $levels;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->menus  = $this('menus')->getMenuRepository();
        $this->items  = $this('menus')->getItemRepository();
        $this->levels = $this('users')->getAccessLevelRepository();
    }

    /**
     * @Request({"id": "int"})
     * @View("system/admin/menu/index.razr.php")
     */
    public function indexAction($id = null)
    {
        $menus = $this->menus->query()->orderBy('name')->get();

        if ($id === null && count($menus)) {
            $menu = current($menus);
        } elseif ($id) {
            $menu = $this->menus->find($id);
        } else {
            $menu = new Menu;
            $menu->setId(0);
        }

        return array('head.title' => __('Menus'), 'menu' => $menu, 'menus' => $menus, 'levels' => $this->levels->findAll(), 'controller' => $this);
    }

    /**
     * @Request({"id": "int", "name"})
     * @Token
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

            if ($this->menus->where(array('name = ?', 'id <> ?'), array($name, $id))->first()) {
                throw new Exception(__('Invalid menu name. "%name%" is already in use.', array('%name%' => $name)));
            }

            $this->menus->save($menu, compact('name'));

        } catch (Exception $e) {
            $this('message')->error($e->getMessage());
        }

        return $this->redirect('@system/menu/index', array('id' => isset($menu) ? $menu->getId() : 0));
    }

    /**
     * @Request({"id": "int"})
     * @Token
     */
    public function deleteAction($id)
    {
        try {

            if (!$menu = $this->menus->find($id)) {
                throw new Exception(__('Invalid menu id'));
            }

            $this->menus->delete($menu);

            $this('db')->delete('@system_menu_item', array('menu_id' => $id));

        } catch (Exception $e) {
            $this('message')->error($e->getMessage());
        }

        return $this->redirect('@system/menu/index');
    }

    /**
     * @Request({"id": "int", "order": "array"})
     * @Token
     */
    public function reorderAction($id, $order = array())
    {
        $items = $this->items->findByMenu($id);

        foreach ($order as $data) {

            if (!isset($items[$data['id']])) {
                continue;
            }

            $item = $items[$data['id']];
            $item->setParentId($data['parent_id']);
            $item->setDepth($data['depth']);
            $item->setPriority($data['order']);

            $this->items->save($item);
        }

        return $this('response')->json('success');
    }

    public function formatUrl($url)
    {
        $url  = $this('url')->to($url);
        $root = $this('url')->root();

        return substr($url, 0, $length = strlen($root)) == $root ? substr($url, $length) : $url;
    }
}
