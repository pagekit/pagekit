<?php

namespace Pagekit\Site;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\Site\Entity\Node;
use Pagekit\Site\Model\AliasType;
use Pagekit\Site\Model\TypeInterface;
use Pagekit\Site\Model\UrlType;

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
        $app->on('kernel.request', function() {
            foreach (Node::where(['status = ?'], [1])->get() as $node) {
                if ($type = $this->getType($node->getType())) {
                    $type->bind($node);
                }
            }
        }, 35);
    }

    /**
     * @param  string $type
     * @return TypeInterface
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

            $this->registerType(new UrlType('alias', __('Alias'), ''));

            App::trigger('site.types', [$this]);
        }

        return $this->types;
    }

    /**
     * Registers a node type.
     *
     * @param TypeInterface $type
     */
    public function registerType(TypeInterface $type)
    {
        $this->types[$type->getId()] = $type;
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

        return array_filter(array_map(function($subsections) use ($type) {
            return array_filter($subsections, function($section) use ($type) { return in_array($section['type'], ['', $type]); });
        }, $this->sections));
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
