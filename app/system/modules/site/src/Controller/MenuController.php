<?php

namespace Pagekit\Site\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Site\Entity\Node;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * @Access("site: manage site")
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
        $menus = $this->get();
        $menus[$id] = compact('id', 'label');
        $this->update($menus);

        return 'success';
    }

    /**
     * @Route("/{id}", methods="PUT")
     * @Request({"id", "label", "oldId"}, csrf=true)
     */
    public function updateAction($id, $label, $oldId)
    {
        $menus = $this->get();

        if ($id != $oldId) {

            if (array_key_exists($id, $menus)) {
                throw new ConflictHttpException(__('Duplicate Menu Id.'));
            }

            $keys = array_keys($menus);
            $keys[array_search($oldId, $keys)] = $id;
            $menus = array_combine($keys, $menus);

            Node::where(['menu = :old'], [':old' => $oldId])->update(['menu' => $id]);
        }

        $menus[$id] = compact('id', 'label');
        $this->update($menus);

        return 'success';
    }

    /**
     * @Route("/{id}", methods="DELETE")
     * @Request({"id"}, csrf=true)
     *
     * TODO: what happens to the nodes?
     */
    public function deleteAction($id)
    {
        $menus = $this->get();
        unset($menus[$id]);
        $this->update($menus);

        return 'success';
    }

    /**
     * @return array
     */
    protected function get()
    {
        return App::module('system/site')->config('menus');
    }

    /**
     * @param array $menus
     */
    protected function update($menus = [])
    {
        $config = App::option()->get('system/site:config', []);
        $config['menus'] = $menus;
        App::option()->set('system/site:config', $config);
    }
}
