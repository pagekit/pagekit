<?php

namespace Pagekit\Site;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\Site\Entity\Node;

class SiteModule extends Module
{
    protected $types;
    protected $menus;
    protected $frontpage;

    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $app['node'] = function ($app) {
            if ($id = $app['request']->attributes->get('_node') and $node = Node::find($id)) {
                return $node;
            }

            return new Node;
        };
    }

    /**
     * @param  string $type
     * @return array
     */
    public function getType($type)
    {
        $types = $this->getTypes();

        return isset($types[$type]) ? $types[$type] : null;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        if (!$this->types) {

            foreach (App::module() as $module) {
                foreach ((array) $module->get('nodes') as $type => $route) {
                    $this->registerType($type, $route);
                }
            }

            $this->registerType('alias', ['label' => __('Alias')]);

            App::trigger('site.types', [$this]);
        }

        return $this->types;
    }

    /**
     * Register a node type.
     *
     * @param string $type
     * @param array  $route
     */
    public function registerType($type, array $route)
    {
        $route['id']        = $type;
        $this->types[$type] = $route;
    }

    /**
     * @param  string $id
     * @return array
     */
    public function getMenu($id)
    {
        $menus = $this->getMenus();

        return isset($menus[$id]) ? $menus[$id] : null;
    }

    /**
     * @return array[]
     */
    public function getMenus()
    {
        if (!$this->menus) {

            foreach (App::module() as $module) {
                foreach ((array) $module->get('menus') as $id => $menu) {
                    $this->registerMenu($id, $menu, ['fixed' => true]);
                }
            }

            foreach ($this->config('menus') as $menu) {
                $this->registerMenu($menu['id'], $menu['label']);
            }

            App::trigger('site.menus', [$this]);

            foreach (Node::where(['menu <> ?', 'menu <> ?'], ['', 'trash'])
                         ->whereIn('menu', array_keys($this->menus), true)
                         ->groupBy('menu')->execute('menu')
                         ->fetchAll(\PDO::FETCH_COLUMN) as $menu
            ) {
                $this->registerMenu($menu, $menu, ['ghost' => true]);
            }

            uasort($this->menus, function ($a, $b) {
                return strcmp($a['label'], $b['label']);
            });

            $this->registerMenu('', 'Not Linked', ['fixed' => true]);
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
     * Gets the site's frontpage route.
     *
     * @return string
     */
    public function getFrontpage()
    {
        return $this->frontpage;
    }

    /**
     * Sets the site's frontpage route.
     *
     * @param string $name
     */
    public function setFrontpage($name)
    {
        $this->frontpage = $name;
    }
}
