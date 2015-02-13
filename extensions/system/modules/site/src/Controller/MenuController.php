<?php

namespace Pagekit\Site\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Site\Entity\Node;

/**
 * @Access("system: manage site")
 * @Response("json")
 */
class MenuController extends Controller
{
    /**
     * @Route("/", methods="GET")
     */
    public function indexAction()
    {
        return array_values(App::module('system/site')->getMenus() + [['id' => '', 'label' => 'Not Linked', 'fixed' => true]]);
    }

    /**
     * @Route("/{id}", methods="POST")
     * @Request({"id", "label"}, csrf=true)
     */
    public function createAction($id, $label)
    {
        $menus = App::option('system:site.menus', []);
        $menus[$id] = compact('id', 'label');
        App::option()->set('system:site.menus', $menus);

        return $menus[$id];
    }

    /**
     * @Route("/{id}", methods="PUT")
     * @Request({"id", "label", "newId"}, csrf=true)
     */
    public function updateAction($oldId, $label, $id)
    {
        $menus = App::option('system:site.menus', []);

        if ($id != $oldId) {
            $keys = array_keys($menus);
            $keys[array_search($oldId, $keys)] = $id;
            $menus = array_combine($keys, $menus);

            Node::where(['menu = :old'], [':old' => $oldId])->update(['menu' => $id]);
        }

        $menus[$id] = compact('id', 'label');
        App::option()->set('system:site.menus', $menus);

        return $menus[$id];
    }

    /**
     * @Route("/{id}", methods="DELETE")
     * @Request({"id"}, csrf=true)
     */
    public function deleteAction($id)
    {
        $menus = App::option('system:site.menus', []);
        unset($menus[$id]);
        App::option()->set('system:site.menus', $menus);
    }
}
