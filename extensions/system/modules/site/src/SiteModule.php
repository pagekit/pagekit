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

            $event = new TypeEvent;
            $event->register('alias', 'Alias', [
                'type'      => 'url',
                'tmpl.edit' => 'alias.edit'
            ]);

            $this->types = App::trigger('site.types', $event)->getTypes();
        }

        return $this->types;
    }

    /**
     * @return array
     */
    public function getMenus()
    {
        if (!$this->menus) {

            $event = new MenuEvent;

            foreach (App::module() as $module) {

                if (!isset($module->menus)) {
                    continue;
                }

                foreach ($module->menus as $id => $menu) {
                    $event->register($id, $menu, ['fixed' => true]);
                }
            }

            $this->menus = App::trigger('site.menus', $event)->getMenus();
        }

        return $this->menus;
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
     * Registers menus option.
     */
    public function onSiteMenus(MenuEvent $event)
    {
        foreach (App::option('system:site.menus', []) as $menu) {
            $event->register($menu['id'], $menu['label']);
        }
    }

    /**
     * Registers alias edit template.
     */
    public function onSystemTmpl(TmplEvent $event)
    {
        $event->register('alias.edit', 'extensions/system/modules/site/views/tmpl/site.alias.php');
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
            'site.menus'     => 'onSiteMenus',
            'system.tmpl'    => 'onSystemTmpl',
            'kernel.request' => ['onKernelRequest', 35]
        ];
    }
}
