<?php

namespace Pagekit\Widget;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\Widget\Model\TypeInterface;

class WidgetModule extends Module
{
    public $types = [];
    public $positions;

    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $app['scripts']->register('widgets', 'widget:app/bundle/widgets.js', 'vue');
        // $this->config->merge(['widget' => ['defaults' => $app['theme.site']->config('widget.defaults', [])]]);

        $app['module']->addFactory('widget', function ($module) use ($app) {

            $class = is_string($module['main']) ? $module['main'] : 'Pagekit\Widget\Model\Type';

            $module = new $class($module);
            $module->main($app);

            $this->registerType($module);

            return $module;
        });
    }

    /**
     * @return array
     */
    public function getPositions()
    {
        if (!$this->positions) {

            $theme = App::get('theme.site');
            $config = $this->config('widget.positions');

            $this->positions = new PositionManager($config);

            foreach ((array) $theme->get('positions') as $name => $position) {
                list($label, $description) = array_merge((array) $position, ['']);
                $this->positions->register($name, $label, $description);
            }
        }

        return $this->positions;
    }

    /**
     * @param  string $type
     * @return TypeInterface
     */
    public function getType($type)
    {
        return isset($this->types[$type]) ? $this->types[$type] : null;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Registers a type.
     *
     * @param TypeInterface $type
     */
    public function registerType(TypeInterface $type)
    {
        $this->types[$type->name] = $type;
    }
}
