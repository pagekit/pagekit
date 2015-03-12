<?php

namespace Pagekit\Site;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\Site\Entity\Node;
use Pagekit\Site\Event\MenuEvent;
use Pagekit\Site\Event\TypeEvent;
use Pagekit\System\Event\TmplEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SiteModule extends Module implements EventSubscriberInterface
{
    protected $types;
    protected $menus;

    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $app->subscribe($this);
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        if (!$this->types) {

            $this->registerType('alias', 'Alias', [
                'type'      => 'url',
                'tmpl.edit' => 'alias.edit'
            ]);

            App::trigger('site.types', [$this]);
        }

        return $this->types;
    }

    /**
     * Registers a node type.
     *
     * @param string $id
     * @param string $label
     * @param array  $options
     */
    public function registerType($id, $label, array $options = [])
    {
        $this->types[$id] = array_merge($options, compact('id', 'label'));
    }

    /**
     * @return array
     */
    public function getMenus()
    {
        if (!$this->menus) {

            foreach (App::module() as $module) {

                if (!isset($module->menus)) {
                    continue;
                }

                foreach ($module->menus as $id => $menu) {
                    $this->registerMenu($id, $menu, ['fixed' => true]);
                }
            }

            foreach (App::option('system:site.menus', []) as $menu) {
                $this->registerMenu($menu['id'], $menu['label']);
            }

            App::trigger('site.menus', [$this]);
        }

        return $this->menus;
    }

    /**
     * Registers a menu.
     *
     * @param string $id
     * @param string $label
     * @param array  $options
     */
    public function registerMenu($id, $label, array $options = [])
    {
        $this->menus[$id] = array_merge($options, compact('id', 'label'));
    }

    /**
     * @return array
     */
    public function getNodes()
    {
        $nodes = [];
        $types = $this->getTypes();

        foreach (Node::where(['status = ?'], [1])->get() as $node) {

            if (!isset($types[$node->getType()])) {
                continue;
            }

            $type = $types[$node->getType()];

            $nodes[$node->getPath()] = [
                'id'          => $type['id'],
                'url'         => $node->get('url', ''),
                'controllers' => isset($type['controllers']) ? $type['controllers'] : '',
                'defaults'    => array_merge_recursive(isset($type['defaults']) ? $type['defaults'] : [], $node->get('defaults', []))
            ];
        }

        return $nodes;
    }

    /**
     * Registers alias edit template.
     */
    public function onSystemLoaded()
    {
        App::view()->tmpl()->register('alias.edit', 'extensions/system/modules/site/views/tmpl/site.alias.php');
    }

    /**
     * Registers node routes.
     */
    public function onKernelRequest()
    {
        foreach ($this->getNodes() as $path => $node) {
            if ($node['controllers']) {
                App::controllers()->mount($path, $node['controllers'], "@{$node['id']}/", $node['defaults']);
            } elseif ($node['url']) {
                App::aliases()->add($path, $node['url'], $node['defaults']);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.loaded'  => 'onSystemLoaded',
            'kernel.request' => ['onKernelRequest', 35]
        ];
    }
}
