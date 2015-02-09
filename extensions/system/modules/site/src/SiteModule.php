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
    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $app['site.types'] = function($app) {
            return $app->trigger('site.types', new TypeEvent)->getTypes();
        };

        $app['site.menus'] = function($app) {

            $event = new MenuEvent;

            foreach ($app['module'] as $module) {

                if (!isset($module->menus)) {
                    continue;
                }

                foreach ($module->menus as $id => $menu) {
                    $event->register($id, $menu, ['fixed' => true]);
                }
            }

            return $app->trigger('site.menus', $event)->getMenus();
        };

        $app->subscribe($this);
    }

    /**
     * @return array
     */
    public function getNodes()
    {
        $nodes = [];
        $types = App::get('site.types');

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
     * Registers alias type.
     */
    public function onSiteTypes(TypeEvent $event)
    {
        $event->register('alias', 'Alias', [
            'type'      => 'url',
            'tmpl.edit' => 'alias.edit'
        ]);
    }

    /**
     * Registers alias type.
     */
    public function onSiteMenus(MenuEvent $event)
    {
        foreach (App::option('system:site.menus', []) as $menu) {
            $event->register($menu['id'], $menu['label']);
        }
    }

    /**
     * Register node routes.
     */
    public function onSystemInit()
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
     * Registers alias edit template.
     */
    public function onSystemTmpl(TmplEvent $event)
    {
        $event->register('alias.edit', 'extensions/system/modules/site/views/tmpl/site.alias.php');
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'site.types'  => 'onSiteTypes',
            'site.menus'  => 'onSiteMenus',
            'system.init' => ['onSystemInit', 10],
            'system.tmpl' => 'onSystemTmpl'
        ];
    }
}
