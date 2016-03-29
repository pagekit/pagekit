<?php

namespace Pagekit\Site;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\Site\Model\Node;

class SiteModule extends Module
{
    protected $types;

    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $app['node'] = function ($app) {

            if ($id = $app['request']->attributes->get('_node') and $node = Node::find($id, true)) {
                return $node;
            }

            return Node::create();
        };

        $app['menu'] = function ($app) {

            $menus = new MenuManager($app->config($app['theme']->name), $this->config('menus'));

            foreach ($app['theme']->get('menus', []) as $name => $label) {
                $menus->register($name, $label);
            }

            return $menus;
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

            $this->registerType('link', ['label' => 'Link', 'frontpage' => false]);

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
        if (isset($route['protected']) and $route['protected'] and !array_filter(Node::findAll(true), function ($node) use ($type) { return $type === $node->type; })) {
            Node::create([
                'title' => $route['label'],
                'slug' => App::filter($route['label'], 'slugify'),
                'type' => $type,
                'status' => 1,
                'link' => $route['name']
            ])->save();
        }

        $route['id'] = $type;
        $this->types[$type] = $route;
    }
}
