<?php

namespace Pagekit\Site\Controller;

use Pagekit\Application as App;
use Pagekit\Kernel\Exception\ConflictException;
use Pagekit\Site\Entity\Node;

/**
 * @Access("site: manage site")
 */
class MenuController
{
    /**
     * @Route("/", methods="GET")
     */
    public function indexAction()
    {
        return array_values(App::module('system/site')->getMenus() + [['id' => '', 'label' => 'Not Linked', 'fixed' => true]]);
    }

    /**
     * @Route("/{label}", methods="POST")
     * @Request({"label", "id"}, csrf=true)
     */
    public function createAction($label, $id = '')
    {
        $menus = $this->get();
        $label = trim($label);

        if (!$id = $this->slugify($id ?: $label)) {
            App::abort(400, __('Invalid id.'));
        }

        if (array_key_exists($id, $menus)) {
            throw new ConflictException(__('Duplicate Menu Id.'));
        }

        $menus[$id] = compact('id', 'label');
        $this->update($menus);

        return ['message' => 'success'];
    }

    /**
     * @Route("/{label}", methods="PUT")
     * @Request({"label", "oldId", "id"}, csrf=true)
     */
    public function updateAction($label, $oldId, $id = '')
    {
        $menus = $this->get();
        $label = trim($label);

        if (!$id = $this->slugify($id ?: $label)) {
            App::abort(400, __('Invalid id.'));
        }

        if ($id != $oldId) {

            if (array_key_exists($id, $menus)) {
                throw new ConflictException(__('Duplicate Menu Id.'));
            }

            $keys = array_keys($menus);
            $keys[array_search($oldId, $keys)] = $id;
            $menus = array_combine($keys, $menus);

            Node::where(['menu = :old'], [':old' => $oldId])->update(['menu' => $id]);
        }

        $menus[$id] = compact('id', 'label');
        $this->update($menus);

        return ['message' => 'success'];
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

        return ['message' => 'success'];
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
        $config = App::config('system/site', []);
        $config['menus'] = $menus;
        App::config()->set('system/site', $config);
    }

    protected function slugify($slug)
    {
        $slug = preg_replace('/\xE3\x80\x80/', ' ', $slug);
        $slug = str_replace('-', ' ', $slug);
        $slug = preg_replace('#[:\#\*"@+=;!><&\.%()\]\/\'\\\\|\[]#', "\x20", $slug);
        $slug = str_replace('?', '', $slug);
        $slug = trim(mb_strtolower($slug, 'UTF-8'));
        $slug = preg_replace('#\x20+#', '-', $slug);

        return $slug;
    }
}
