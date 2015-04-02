<?php

namespace Pagekit\Site;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\Site\Event\RouteListener;

class SiteModule extends Module
{
    protected $types;
    protected $menus;
    protected $sections;

    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $app->subscribe(new RouteListener());
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        if (!$this->types) {

            $this->registerType('alias', 'Alias', 'url');

            App::trigger('site.types', [$this]);
        }

        return $this->types;
    }

    /**
     * Registers a node type.
     *
     * @param string $id
     * @param string $label
     * @param string $type
     * @param array  $options
     */
    public function registerType($id, $label, $type, array $options = [])
    {
        $this->types[$id] = array_merge($options, compact('id', 'label', 'type'));
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

            foreach ($this->config('menus') as $menu) {
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
     * @param  string $type
     * @return array
     */
    public function getSections($type = '')
    {
        if (!$this->sections) {

            $this->registerSection('Settings', 'site:views/admin/settings.php');
            $this->registerSection('Settings', 'site:views/admin/alias.php', 'alias');

            App::trigger('site.sections', [$this]);
        }

        return array_map(function($subsections) use ($type) {
            return array_filter($subsections, function($section) use ($type) { return in_array($section['type'], ['', $type]); });
        }, $this->sections);
    }

    /**
     * Registers a settings section.
     *
     * @param string $name
     * @param string $view
     * @param string $type
     * @param array  $options
     */
    public function registerSection($name, $view, $type = '', array $options = [])
    {
        $this->sections[$name][] = array_merge($options, compact('name', 'view', 'type'));
    }
}
