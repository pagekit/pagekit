<?php

namespace Pagekit\Site\Controller;

use Pagekit\Application as App;
use Pagekit\Config\Config;
use Pagekit\Kernel\Exception\ConflictException;
use Pagekit\Site\Model\Node;

/**
 * @Access("site: manage site")
 */
class MenuController
{
    /**
     * @var Config
     */
    protected $config;

    public function __construct()
    {
        $this->config = App::config('system/site');
    }

    /**
     * @Route("/", methods="GET")
     */
    public function indexAction()
    {
        $menus = App::menus()->get();

        $menus['trash'] = ['id' => 'trash', 'label' => 'Trash', 'fixed' => true];

        foreach ($menus as &$menu) {
            $menu['count'] = Node::where(['menu' => $menu['id']])->count();
        }

        if (!$menus['trash']['count']) {
            unset($menus['trash']);
        }

        return array_values($menus);
    }

    /**
     * @Route("/{label}", methods="POST")
     * @Request({"label", "id"}, csrf=true)
     */
    public function createAction($label, $id = '')
    {
        $label = trim($label);

        if (!$id = $this->slugify($id ?: $label)) {
            App::abort(400, __('Invalid id.'));
        }

        if ($this->config->has('menus.'.$id)) {
            throw new ConflictException(__('Duplicate Menu Id.'));
        }

        $this->config->merge(['menus' => [$id => compact('id', 'label')]]);

        return ['message' => 'success'];
    }

    /**
     * @Route("/{label}", methods="PUT")
     * @Request({"label", "oldId", "id"}, csrf=true)
     */
    public function updateAction($label, $oldId, $id = '')
    {
        $label = trim($label);

        if (!$id = $this->slugify($id ?: $label)) {
            App::abort(400, __('Invalid id.'));
        }

        if ($id != $oldId) {

            if ($this->config->has('menus.'.$id)) {
                throw new ConflictException(__('Duplicate Menu Id.'));
            }

            $this->config->remove('menus.'.$oldId);
            Node::where(['menu = :old'], [':old' => $oldId])->update(['menu' => $id]);
        }

        $this->config->set('menus.'.$id, compact('id', 'label'));

        return ['message' => 'success'];
    }

    /**
     * @Route("/{id}", methods="DELETE")
     * @Request({"id"}, csrf=true)
     */
    public function deleteAction($id)
    {
        App::config('system/site')->remove('menus.'.$id);
        Node::where(['menu = :id'], [':id' => $id])->update(['menu' => 'trash', 'status' => 0]);

        return ['message' => 'success'];
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
